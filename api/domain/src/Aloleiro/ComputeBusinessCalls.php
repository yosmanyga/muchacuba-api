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
     * @param int    $from
     * @param int    $to
     *
     * @return array
     *
     * @throws \Exception
     */
    public function compute($uniqueness, $from, $to)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        $stats = $this->computeCalls->compute(
            $profile->getBusiness(),
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