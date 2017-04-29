<?php

namespace Cubalider\Call\Provider;

interface ListenDisconnectCallEvent
{
    const RESULT_ANSWERED = 'ANSWERED';
    const RESULT_BUSY = 'BUSY';
    const RESULT_NOANSWER = 'NOANSWER';
    const RESULT_FAILED = 'FAILED';

    /**
     * @param string $callId
     * @param int    $timestamp
     * @param int    $duration
     * @param float  $cost
     *
     * @return Response|null
     */
    public function listen($callId, $timestamp, $result, $duration, $cost);
}