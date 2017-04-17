<?php

namespace Cubalider\Call\Provider;

interface ListenSummaryCallEvent
{
    /**
     * @param string $callId
     * @param int    $duration
     * @param float  $cost
     */
    public function listen($callId, $duration, $cost);
}