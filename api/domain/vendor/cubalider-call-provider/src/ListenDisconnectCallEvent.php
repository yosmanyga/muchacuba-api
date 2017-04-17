<?php

namespace Cubalider\Call\Provider;

interface ListenDisconnectCallEvent
{
    /**
     * @param string $callId
     *
     * @return Response|null
     */
    public function listen($callId);
}