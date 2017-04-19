<?php

namespace Cubalider\Call\Provider;

interface ListenDisconnectCallEvent
{
    /**
     * @param string $callId
     * @param int    $timestamp
     * @param int    $duration
     * @param float  $cost
     *
     * @return Response|null
     */
    public function listen($callId, $timestamp, $duration, $cost);
}