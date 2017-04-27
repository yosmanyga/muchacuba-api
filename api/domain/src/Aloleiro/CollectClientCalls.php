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
     * @param string    $uniqueness
     * @param int|null  $from
     * @param int |null $to
     *
     * @return ClientCall[]
     */
    public function collect($uniqueness, $from = null, $to = null)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        $criteria = [];

        $criteria['business'] = $profile->getBusiness();

        if (!is_null($from)) {
            $criteria['instances.timestamp']['$gte'] = new UTCDateTime($from * 1000);
        }

        if (!is_null($to)) {
            $criteria['instances.timestamp']['$lt'] = new UTCDateTime($to * 1000);
        }

        /** @var Call[] $calls */
        $calls = $this->manageStorage->connect()->find(
            $criteria,
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
                    $instance['timestamp'] ? (string) $instance['timestamp'] / 1000 : null, //$instance->getTimestamp(),
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
