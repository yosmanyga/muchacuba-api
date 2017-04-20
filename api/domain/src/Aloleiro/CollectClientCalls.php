<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\UTCDateTime;
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
     * @param int    $from
     * @param int    $to
     *
     * @return ClientCall[]
     */
    public function collect($uniqueness, $from, $to)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        /** @var Call[] $calls */
        $calls = $this->manageStorage->connect()->find(
            [
                'business' => $profile->getBusiness(),
                'instances.timestamp' => [
                    '$gte' => new UTCDateTime($from * 1000),
                    '$lt' => new UTCDatetime($to * 1000),
                ]
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
