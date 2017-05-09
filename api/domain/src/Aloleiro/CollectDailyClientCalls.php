<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectDailyClientCalls
{
    /**
     * @var CollectClientCalls
     */
    private $collectClientCalls;

    /**
     * @param CollectClientCalls $collectClientCalls
     */
    public function __construct(CollectClientCalls $collectClientCalls)
    {
        $this->collectClientCalls = $collectClientCalls;
    }

    /**
     * @param Business $business
     *
     * @return ClientCall[]
     */
    public function collect(Business $business)
    {
        $now = new \DateTime("now");
        $from = clone $now;
        $from->modify('today');
        $to = clone $from;
        $to->modify('tomorrow');

        return $this->collectClientCalls->collect(
            $business,
            $from->getTimestamp(),
            $to->getTimestamp()
        );
    }
}
