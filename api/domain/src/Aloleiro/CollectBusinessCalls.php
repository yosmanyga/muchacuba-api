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
     * @param string $uniqueness
     *
     * @return BusinessCall[]
     */
    public function collect($uniqueness)
    {
        /** @var Call[] $calls */
        $calls = $this->manageStorage->connect()->find([
            'uniqueness' => $uniqueness
        ]);

        $businessCalls = [];

        foreach ($calls as $call) {
            $instances = [];
            foreach ($call->getInstances() as $instance) {
                $instances[] = new BusinessInstance(
                    $instance['duration'], //$instance->getDuration(),
                    $instance['businessPurchase'], //$instance->getBusinessPurchase(),
                    $instance['businessSale'] //$instance->getBusinessSale()
                );
            }

            $businessCalls[] = new BusinessCall(
                $call->getFrom(),
                $call->getTo(),
                $instances
            );
        }

        return $businessCalls;
    }
}
