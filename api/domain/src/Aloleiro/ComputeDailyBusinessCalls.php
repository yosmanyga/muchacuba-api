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
        $now = new \DateTime("now", new \DateTimeZone('America/Caracas') );
        $from = clone $now;
        $from->modify('today');
        $to = clone $from;
        $to->modify('tomorrow');

        $stats = $this->computeCalls->compute(
            $uniqueness,
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