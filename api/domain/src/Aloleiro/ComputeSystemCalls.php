<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ComputeSystemCalls
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
     * @param int $from
     * @param int $to
     * 
     * @return array
     *
     * @throws \Exception
     */
    public function compute($from, $to)
    {
        $stats = $this->computeCalls->compute(
            null,
            $from,
            $to,
            ComputeCalls::GROUP_BY_DAY
        );

        $systemStats = [];
        foreach ($stats as $stat) {
            $systemStats[] = [
                'duration' => $stat['duration'],
                'purchase' => $stat['systemPurchase'],
                'sale' => $stat['systemSale'],
                'profit' => $stat['systemProfit'],
                'total' => $stat['total'],
                'year' => $stat['year'],
                'month' => $stat['month'],
                'day' => $stat['day'],
            ];
        }

        return $systemStats;
    }
}