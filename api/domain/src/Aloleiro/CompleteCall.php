<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Voip\ListenCompletedEvent;
use MongoDB\BSON\UTCDateTime;
use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Business\DecreaseBalance;
use Muchacuba\Aloleiro\Call\Instance;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;

/**
 * @di\service({
 *     deductible: true,
 *     tags: ['cubalider.voip.listen_completed_event']
 * })
 */
class CompleteCall implements ListenCompletedEvent
{
    /**
     * @var PickBusiness
     */
    private $pickBusiness;

    /**
     * @var PickCurrency
     */
    private $pickCurrency;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @var int
     */
    private $profitPercent;

    /**
     * @var DecreaseBalance
     */
    private $decreaseBalance;

    /**
     * @param PickBusiness      $pickBusiness
     * @param PickCurrency      $pickCurrency
     * @param ManageCallStorage $manageCallStorage
     * @param int               $profitPercent
     * @param DecreaseBalance   $decreaseBalance
     *
     * @di\arguments({
     *     profitPercent: "%profit_percent%",
     * })
     */
    public function __construct(
        PickBusiness $pickBusiness,
        PickCurrency $pickCurrency,
        ManageCallStorage $manageCallStorage,
        $profitPercent,
        DecreaseBalance $decreaseBalance
    )
    {
        $this->pickBusiness = $pickBusiness;
        $this->pickCurrency = $pickCurrency;
        $this->manageCallStorage = $manageCallStorage;
        $this->profitPercent = $profitPercent;
        $this->decreaseBalance = $decreaseBalance;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($id, $start, $end, $duration, $cost)
    {
        if ($cost == 0) {
            // Forget about the call if there was no cost

            /** @var UpdateResult $result */
            $result = $this->manageCallStorage->connect()->updateOne(
                ['instances.id' => $id],
                ['$pull' => [
                    'instances' => ['id' => $id]
                ]]
            );

            if ($result->getModifiedCount() == 0) {
                throw new \Exception(sprintf("Instance '%s' does not exist", $id));
            }

            return;
        }

        /** @var Call $call */
        $call = $this->manageCallStorage->connect()->findOne([
            'instances.id' => $id
        ]);

        $business = $this->pickBusiness->pick($call->getBusiness());

        $eurCurrencyExchange = $this->pickCurrency->pickEUR();
        $vefCurrencyExchange = $this->pickCurrency->pickVEF();

        // Initial cost
        $systemPurchase = $cost;
        //
        $systemPurchase = $systemPurchase / $eurCurrencyExchange;
        // Plus profit
        $systemSale = $systemPurchase + $systemPurchase * $this->profitPercent / 100;
        // Round
        $systemSale = round($systemSale, 4);

        $systemProfit = $systemSale - $systemPurchase;

        // Initial purchase
        $businessPurchase = $systemSale;
        // Currency exchange
        $businessPurchase = $businessPurchase * $vefCurrencyExchange;
        // Round
        $businessPurchase = round($businessPurchase);

        // Initial purchase
        $businessSale = $businessPurchase;
        // Plus profit
        $businessSale = $businessSale + $businessSale * $business->getProfitPercent() / 100;
        // Round
        $businessSale = round($businessSale);

        $businessProfit = $businessSale - $businessPurchase;

        /** @var UpdateResult $result */
        $result = $this->manageCallStorage->connect()->updateOne(
            ['instances.id' => $id],
            ['$set' => [
                'instances.$.start' => new UTCDateTime($start * 1000),
                'instances.$.end' => new UTCDateTime($end * 1000),
                'instances.$.duration' => $duration,
                'instances.$.result' => Instance::RESULT_DID_SPEAK,
                'instances.$.systemPurchase' => $systemPurchase,
                'instances.$.systemSale' => $systemSale,
                'instances.$.systemProfit' => $systemProfit,
                'instances.$.businessPurchase' => $businessPurchase,
                'instances.$.businessSale' => $businessSale,
                'instances.$.businessProfit' => $businessProfit,
            ]]
        );

        if ($result->getMatchedCount() == 0) {
            throw new \Exception(sprintf("Instance '%s' does not exist", $id));
        }

        $this->decreaseBalance->decrease($business, $businessPurchase);
    }
}