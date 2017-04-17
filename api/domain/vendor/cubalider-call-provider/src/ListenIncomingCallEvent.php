<?php

namespace Cubalider\Call\Provider;

interface ListenIncomingCallEvent
{
    /**
     * @param string $from
     * @param string $callId
     *
     * @return Response|null
     */
    public function listen($from, $callId);
}