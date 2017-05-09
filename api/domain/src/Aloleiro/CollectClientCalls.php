<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\UTCDateTime;
use Muchacuba\Aloleiro\Call\Instance;
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
     * @param Business  $business
     * @param int|null  $from
     * @param int |null $to
     *
     * @return ClientCall[]
     */
    public function collect(Business $business, $from = null, $to = null)
    {
        $criteria = [];

        $criteria['business'] = $business->getId();

        if (!is_null($from)) {
            $criteria['instances.start']['$gte'] = new UTCDateTime($from * 1000);
        }

        if (!is_null($to)) {
            $criteria['instances.start']['$lt'] = new UTCDateTime($to * 1000);
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
                    $instance['id'],
                    $instance['start'] ? (string) $instance['start'] / 1000 : null, //$instance->getStart(),
                    $instance['end'] ? (string) $instance['end'] / 1000 : null, //$instance->getEnd(),
                    $instance['duration'], //$instance->getDuration(),
                    $instance['result'],
                    $instance['businessSale'] //$instance->getBusinessSale()
                );
            }

            // Newest first
            $instances = array_reverse($instances);

            $clientCalls[] = new ClientCall(
                $call->getId(),
                $call->getFrom(),
                $call->getTo(),
                $instances
            );
        }

        return $clientCalls;
    }
}
