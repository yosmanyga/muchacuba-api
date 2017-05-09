<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ManageStorage;
use Muchacuba\Aloleiro\Call\BusinessInstance;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectBusinessCalls
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param Business $business
     *
     * @return BusinessCall[]
     */
    public function collect(Business $business)
    {
        /** @var Call[] $calls */
        $calls = $this->manageStorage->connect()->find(
            [
                'business' => $business->getId()
            ],
            [
                'sort' => [
                    '_id' => -1
                ]
            ]
        );

        $businessCalls = [];

        foreach ($calls as $call) {
            $instances = [];
            foreach ($call->getInstances() as $instance) {
                $instances[] = new BusinessInstance(
                    $instance['start'] ? (string) $instance['start'] / 1000 : null, //$instance->getStart(),
                    $instance['end'] ? (string) $instance['end'] / 1000 : null, //$instance->getEnd(),
                    $instance['duration'], //$instance->getDuration(),
                    $instance['businessPurchase'], //$instance->getBusinessPurchase(),
                    $instance['businessSale'], //$instance->getBusinessSale()
                    $instance['businessProfit'] //$instance->getBusinessProfit()
                );
            }

            // Newest first
            $instances = array_reverse($instances);

            $businessCalls[] = new BusinessCall(
                $call->getFrom(),
                $call->getTo(),
                $instances
            );
        }

        return $businessCalls;
    }
}
