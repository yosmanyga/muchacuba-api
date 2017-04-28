<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ComputeMonthlySystemCalls
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
     * @return array
     *
     * @throws \Exception
     */
    public function compute()
    {
        $now = new \DateTime("now", new \DateTimeZone('America/Caracas') );
        $from = clone $now;
        $from->modify('first day of this month');
        $from->modify('midnight');
        $to = clone $from;
        $to->modify('last day of this month');
        $to->modify('tomorrow');

        $stats = $this->computeCalls->compute(
            null,
            $from->getTimestamp(),
            $to->getTimestamp(),
            ComputeCalls::GROUP_BY_DAY
        );

        $monthlySystemStats = [];
        foreach ($stats as $stat) {
            $monthlySystemStats[] = [
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

        return $monthlySystemStats;
    }
}