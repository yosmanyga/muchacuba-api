<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ComputeBusinessCalls
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
     * @param Business $business
     * @param int      $from
     * @param int      $to
     *
     * @return array
     *
     * @throws \Exception
     */
    public function compute(Business $business, $from, $to)
    {
        $stats = $this->computeCalls->compute(
            $business,
            $from,
            $to,
            ComputeCalls::GROUP_BY_DAY
        );

        $monthlyBusinessStats = [];
        foreach ($stats as $stat) {
            $monthlyBusinessStats[] = [
                'duration' => $stat['duration'],
                'purchase' => $stat['businessPurchase'],
                'sale' => $stat['businessSale'],
                'profit' => $stat['businessProfit'],
                'total' => $stat['total'],
                'year' => $stat['year'],
                'month' => $stat['month'],
                'day' => $stat['day'],
            ];
        }

        return $monthlyBusinessStats;
    }
}