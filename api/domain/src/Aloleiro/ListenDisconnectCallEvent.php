<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Call\Provider\NullResponse;
use Cubalider\Call\Provider\ListenDisconnectCallEvent as BaseListenDisconnectCallEvent;
use MongoDB\UpdateResult;
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
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @var int
     */
    private $profitPercent;

    /**
     * @param PickBusiness      $pickBusiness
     * @param ManageCallStorage $manageCallStorage
     * @param int               $profitPercent
     *
     * @di\arguments({
     *     profitPercent: "%profit_percent%",
     * })
     */
    public function __construct(
        PickBusiness $pickBusiness,
        ManageCallStorage $manageCallStorage,
        $profitPercent
    )
    {
        $this->pickBusiness = $pickBusiness;
        $this->manageCallStorage = $manageCallStorage;
        $this->profitPercent = $profitPercent;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($cid, $duration, $cost)
    {
        /** @var Call $call */
        $call = $this->manageCallStorage->connect()->findOne([
            'instances.id' => $cid
        ]);

        $business = $this->pickBusiness->pick($call->getBusiness());

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
        $businessPurchase = $businessPurchase * $business->getCurrencyExchange();
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

        return new NullResponse();
    }
}