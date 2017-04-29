<?php

namespace Cubalider\Call\Provider;

interface ListenAnswerCallEvent
{
    /**
     * @param string $callId
     * @param int    $timestamp
     *
     * @return Response|null
     */
    public function listen($callId, $timestamp);
}