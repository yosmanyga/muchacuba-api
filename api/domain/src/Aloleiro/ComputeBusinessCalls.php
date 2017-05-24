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
     * @param string   $by
     *
     * @return array
     *
     * @throws \Exception
     */
    public function compute(
        Business $business,
        $from,
        $to,
        $by
    )
    {
        $stats = $this->computeCalls->compute(
            $business,
            $from,
            $to,
            $by
        );

        if ($by == ComputeCalls::GROUP_BY_DAY) {
            $businessStats = [];
            foreach ($stats as $stat) {
                $businessStats[] = [
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
        } elseif ($by == ComputeCalls::GROUP_BY_MONTH) {
            $businessStats = [];
            foreach ($stats as $stat) {
                $businessStats[] = [
                    'duration' => $stat['duration'],
                    'purchase' => $stat['businessPurchase'],
                    'sale' => $stat['businessSale'],
                    'profit' => $stat['businessProfit'],
                    'total' => $stat['total'],
                    'year' => $stat['year'],
                    'month' => $stat['month']
                ];
            }
        } else {
            $businessStats = [];
            foreach ($stats as $stat) {
                $businessStats[] = [
                    'duration' => $stat['duration'],
                    'purchase' => $stat['businessPurchase'],
                    'sale' => $stat['businessSale'],
                    'profit' => $stat['businessProfit'],
                    'total' => $stat['total'],
                    'year' => $stat['year']
                ];
            }
        }

        return $businessStats;
    }
}