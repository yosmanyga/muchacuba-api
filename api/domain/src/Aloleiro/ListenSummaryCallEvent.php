<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Call\Provider\ListenSummaryCallEvent as BaseListenSummaryCallEvent;
use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Owner\PickProfile;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.listen_summary_call_event',
 *          key: 'aloleiro.business'
 *     }]
 * })
 */
class ListenSummaryCallEvent implements BaseListenSummaryCallEvent
{
    /**
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @var int
     */
    private $profitFactor;

    /**
     * @var float
     */
    private $currencyExchange;

    /**
     * @param PickProfile       $pickProfile
     * @param ManageCallStorage $manageCallStorage
     * @param int               $profitFactor
     * @param float             $currencyExchange
     *
     * @di\arguments({
     *     profitFactor:     "%profit_factor%",
     *     currencyExchange: "%currency_exchange%"
     * })
     */
    public function __construct(
        PickProfile $pickProfile,
        ManageCallStorage $manageCallStorage,
        $profitFactor,
        $currencyExchange
    )
    {
        $this->pickProfile = $pickProfile;
        $this->manageCallStorage = $manageCallStorage;
        $this->profitFactor = $profitFactor;
        $this->currencyExchange = $currencyExchange;
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

        $profile = $this->pickProfile->pick(null, $call->getBusiness());

        // Purchase
        $systemPurchase = $cost;

        // Purchase plus profit
        $systemSale = $systemPurchase + $systemPurchase * $this->profitFactor / 100;

        // Purchase
        $businessPurchase = $systemSale;

        // Purchase plus profit
        $businessSale = $businessPurchase * $profile->getProfitFactor() / 100;

        /** @var UpdateResult $result */
        $result = $this->manageCallStorage->connect()->updateOne(
            ['instances.id' => $cid],
            [
                'instances.$.duration' => $duration,
                'instances.$.systemPurchase' => round($systemPurchase),
                'instances.$.systemSale' => round($systemSale),
                'instances.$.businessPurchase' => round($businessPurchase * $this->currencyExchange),
                'instances.$.businessSale' => round($businessSale * $this->currencyExchange),
            ]
        );

        if ($result->getModifiedCount() == 0) {
            throw new \Exception(sprintf("Instance '%s' does not exist", $cid));
        }
    }
}