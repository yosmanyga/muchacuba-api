<?php

namespace Cubalider\Call\Provider;

interface ListenDisconnectCallEvent
{
    /**
     * @param string $callId
     * @param int    $duration
     * @param float  $cost
     *
     * @return Response|null
     */
    public function listen($callId, $duration, $cost);
}