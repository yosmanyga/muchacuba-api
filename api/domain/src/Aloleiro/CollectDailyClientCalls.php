<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Voip\PostProcessCalls;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectDailyClientCalls
{
    /**
     * @var PostProcessCalls
     */
    private $postProcessCalls;

    /**
     * @var CollectClientCalls
     */
    private $collectClientCalls;

    /**
     * @param PostProcessCalls   $postProcessCalls
     * @param CollectClientCalls $collectClientCalls
     */
    public function __construct(
        PostProcessCalls $postProcessCalls,
        CollectClientCalls $collectClientCalls
    )
    {
        $this->postProcessCalls = $postProcessCalls;
        $this->collectClientCalls = $collectClientCalls;
    }

    /**
     * @param Business $business
     *
     * @return ClientCall[]
     */
    public function collect(Business $business)
    {
        // Shortcut to post process calls without waiting for cron
        $this->postProcessCalls->postProcess();

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
