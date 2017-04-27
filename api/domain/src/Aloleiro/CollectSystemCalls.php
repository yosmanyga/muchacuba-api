<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ManageStorage;
use Muchacuba\Aloleiro\Call\SystemInstance;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectSystemCalls
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
     * @return SystemCall[]
     */
    public function collect()
    {
        /** @var Call[] $calls */
        $calls = $this->manageStorage->connect()->find(
            [],
            [
                'sort' => [
                    '_id' => -1
                ]
            ]
        );

        $systemCalls = [];

        foreach ($calls as $call) {
            $instances = [];
            foreach ($call->getInstances() as $instance) {
                $instances[] = new SystemInstance(
                    $instance['timestamp'] ? (string) $instance['timestamp'] / 1000 : null, //$instance->getTimestamp(),
                    $instance['duration'], //$instance->getDuration(),
                    $instance['systemPurchase'], //$instance->getSystemPurchase(),
                    $instance['systemSale'], //$instance->getSystemSale()
                    $instance['systemProfit'] //$instance->getSystemProfit()
                );
            }

            // Newest first
            $instances = array_reverse($instances);

            $systemCalls[] = new SystemCall(
                $call->getFrom(),
                $call->getTo(),
                $instances
            );
        }

        return $systemCalls;
    }
}
