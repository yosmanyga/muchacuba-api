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
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var ComputeCalls
     */
    private $computeCalls;

    /**
     * @param PickProfile  $pickProfile
     * @param ComputeCalls $computeCalls
     */
    public function __construct(
        PickProfile $pickProfile,
        ComputeCalls $computeCalls
    )
    {
        $this->pickProfile = $pickProfile;
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
        $profile = $this->pickProfile->pick($uniqueness);

        $now = new \DateTime("now");
        $from = clone $now;
        $from->modify('first day of this month');
        $from->modify('midnight');
        $to = clone $from;
        $to->modify('last day of this month');
        $to->modify('tomorrow');

        $stats = $this->computeCalls->compute(
            $profile->getBusiness(),
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