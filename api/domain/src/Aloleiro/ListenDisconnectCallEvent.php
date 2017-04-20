<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Call\Provider\NullResponse;
use Cubalider\Call\Provider\ListenDisconnectCallEvent as BaseListenDisconnectCallEvent;
use MongoDB\BSON\UTCDateTime;
use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Business\DecreaseBalance;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.listen_disconnect_call_event',
 *          key: 'aloleiro.business'
 *     }]
 * })
 */
class ListenDisconnectCallEvent implements BaseListenDisconnectCallEvent
{
    /**
     * @var PickBusiness
     */
    private $pickBusiness;

    /**
     * @var PickRate
     */
    private $pickRate;

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
     * @param PickRate          $pickRate
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
        PickRate $pickRate,
        ManageCallStorage $manageCallStorage,
        $profitPercent,
        DecreaseBalance $decreaseBalance
    )
    {
        $this->pickBusiness = $pickBusiness;
        $this->pickRate = $pickRate;
        $this->manageCallStorage = $manageCallStorage;
        $this->profitPercent = $profitPercent;
        $this->decreaseBalance = $decreaseBalance;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($cid, $timestamp, $duration, $cost)
    {
        /** @var Call $call */
        $call = $this->manageCallStorage->connect()->findOne([
            'instances.id' => $cid
        ]);

        $business = $this->pickBusiness->pick($call->getBusiness());

        $currencyExchange = $this->pickRate
            ->pick('Venezuela')
            ->getCountryCurrencyExchange();

        // Initial cost
        $systemPurchase = $cost;
        // Plus profit
        $systemSale = $systemPurchase + $systemPurchase * $this->profitPercent / 100;
        // Round
        $systemSale = round($systemSale, 4);

        $systemProfit = $systemSale - $systemPurchase;

        // Initial purchase
        $businessPurchase = $systemSale;
        // Currency exchange
        $businessPurchase = $businessPurchase * $currencyExchange;
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
            ['instances.id' => $cid],
            ['$set' => [
                'instances.$.timestamp' => new UTCDateTime($timestamp * 1000),
                'instances.$.duration' => $duration,
                'instances.$.systemPurchase' => $systemPurchase,
                'instances.$.systemSale' => $systemSale,
                'instances.$.systemProfit' => $systemProfit,
                'instances.$.businessPurchase' => $businessPurchase,
                'instances.$.businessSale' => $businessSale,
                'instances.$.businessProfit' => $businessProfit,
            ]]
        );

        if ($result->getMatchedCount() == 0) {
            throw new \Exception(sprintf("Instance '%s' does not exist", $cid));
        }

        $this->decreaseBalance->decrease($business, $businessPurchase);

        return new NullResponse();
    }
}