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
     * @param string $uniqueness
     *
     * @return ClientCall[]
     */
    public function collect($uniqueness)
    {
        $now = new \DateTime("now");
        $from = clone $now;
        $from->modify('today');
        $to = clone $from;
        $to->modify('tomorrow');

        return $this->collectClientCalls->collect(
            $uniqueness,
            $from->getTimestamp(),
            $to->getTimestamp()
        );
    }
}
