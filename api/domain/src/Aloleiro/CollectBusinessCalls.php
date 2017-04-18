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
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param PickProfile   $pickProfile
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        PickProfile $pickProfile,
        ManageStorage $manageStorage
    )
    {
        $this->pickProfile = $pickProfile;
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $uniqueness
     *
     * @return BusinessCall[]
     */
    public function collect($uniqueness)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        /** @var Call[] $calls */
        $calls = $this->manageStorage->connect()->find(
            [
                'business' => $profile->getBusiness()
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
                    $instance['duration'], //$instance->getDuration(),
                    $instance['businessPurchase'], //$instance->getBusinessPurchase(),
                    $instance['businessSale'], //$instance->getBusinessSale()
                    $instance['businessProfit'] //$instance->getBusinessProfit()
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
