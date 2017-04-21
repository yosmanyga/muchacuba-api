<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ComputeMonthlyBusinessCalls
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
        $now = new \DateTime("now", new \DateTimeZone('America/Caracas') );
        $from = clone $now;
        $from->modify('first day of this month');
        $from->modify('midnight');
        $to = clone $from;
        $to->modify('last day of this month');
        $to->modify('tomorrow');

        $stats = $this->computeCalls->compute(
            $uniqueness,
            $from->getTimestamp(),
            $to->getTimestamp(),
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