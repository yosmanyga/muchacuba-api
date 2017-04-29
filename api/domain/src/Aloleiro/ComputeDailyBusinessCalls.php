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
        $from->modify('today');
        $to = clone $from;
        $to->modify('tomorrow');

        $stats = $this->computeCalls->compute(
            $profile->getBusiness(),
            $from->getTimestamp(),
            $to->getTimestamp(),
            ComputeCalls::GROUP_BY_DAY
        );

        if (!empty($stats)) {
            $stats = [
                'total' => $stats[0]['total'],
                'sale' => $stats[0]['businessSale'],
            ];
        } else {
            $stats = [
                'total' => 0,
                'sale' => 0
            ];
        }

        return $stats;
    }
}