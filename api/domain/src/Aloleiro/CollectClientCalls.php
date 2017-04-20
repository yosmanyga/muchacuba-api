<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ManageStorage;
use Muchacuba\Aloleiro\Call\ClientInstance;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectClientCalls
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
     * @return ClientCall[]
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

        $clientCalls = [];

        foreach ($calls as $call) {
            $instances = [];
            foreach ($call->getInstances() as $instance) {
                $instances[] = new ClientInstance(
                    (string) $instance['timestamp'] / 1000, //$instance->getTimestamp(),
                    $instance['duration'], //$instance->getDuration(),
                    $instance['businessSale'] //$instance->getBusinessSale()
                );
            }

            // Newest first
            $instances = array_reverse($instances);

            $clientCalls[] = new ClientCall(
                $call->getFrom(),
                $call->getTo(),
                $instances
            );
        }

        return $clientCalls;
    }
}
