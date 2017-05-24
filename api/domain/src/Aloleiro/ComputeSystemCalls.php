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
     * @param int $by
     *
     * @return array
     *
     * @throws \Exception
     */
    public function compute(
        $from,
        $to,
        $by
    )
    {
        $stats = $this->computeCalls->compute(
            null,
            $from,
            $to,
            $by
        );

        if ($by == ComputeCalls::GROUP_BY_DAY) {
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
        } elseif ($by == ComputeCalls::GROUP_BY_MONTH) {
            $systemStats = [];
            foreach ($stats as $stat) {
                $systemStats[] = [
                    'duration' => $stat['duration'],
                    'purchase' => $stat['systemPurchase'],
                    'sale' => $stat['systemSale'],
                    'profit' => $stat['systemProfit'],
                    'total' => $stat['total'],
                    'year' => $stat['year'],
                    'month' => $stat['month']
                ];
            }
        } else {
            $systemStats = [];
            foreach ($stats as $stat) {
                $systemStats[] = [
                    'duration' => $stat['duration'],
                    'purchase' => $stat['systemPurchase'],
                    'sale' => $stat['systemSale'],
                    'profit' => $stat['systemProfit'],
                    'total' => $stat['total'],
                    'month' => $stat['month']
                ];
            }
        }

        return $systemStats;
    }
}