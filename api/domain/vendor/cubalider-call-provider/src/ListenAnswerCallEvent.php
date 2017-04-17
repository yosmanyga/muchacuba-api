<?php

namespace Cubalider\Call\Provider;

interface ListenAnswerCallEvent
{
    /**
     * @param string $callId
     *
     * @return Response|null
     */
    public function listen($callId);
}