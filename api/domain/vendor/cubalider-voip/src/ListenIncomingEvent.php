<?php

namespace Cubalider\Voip;

interface ListenIncomingEvent
{
    /**
     * @param string $from The phone number the call is coming from
     * @param string $id   The internal call id, not the provider call id
     *
     * @return ConnectResponse|HangupResponse
     */
    public function listen($from, $id);
}