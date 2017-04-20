<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ComputeDailyBusinessCalls
{
    /**
     * @var ComputeCalls
     */
    private $computeCalls;

    /**
     * @param ComputeCalls $computeCalls
     */
    public function __construct(
        ComputeCalls $computeCalls
    )
    {
        $this->computeCalls = $computeCalls;
    }

    /**
     * @param string $uniqueness
     *
     * @return array
     *
     * @throws \Exception
     */
    public function compute($uniqueness)
    {
        $now = new \DateTime("now", new \DateTimeZone('America/Los_Angeles') );
        $from = clone $now;
        $from->modify('today');
        $to = clone $from;
        $to->modify('tomorrow');

        $stats = $this->computeCalls->compute(
            $uniqueness,
            $from->getTimestamp() - 1300000,
            $to->getTimestamp(),
            ComputeCalls::GROUP_BY_DAY
        );

        foreach ($stats as $i => $stat) {
            $stat['sale'] = $stat['businessSale'];

            unset($stat['systemProfit']);
            unset($stat['systemPurchase']);
            unset($stat['systemSale']);
            // Also need to remove this, because it's for the operator
            unset($stat['businessProfit']);
            unset($stat['businessPurchase']);
            unset($stat['businessSale']);

            $stats[$i] = $stat;
        }

        return $stats;
    }
}