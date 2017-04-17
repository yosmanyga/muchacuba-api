<?php

namespace Cubalider\Call\Provider;

interface ProcessEvent
{
    /**
     * @param array $payload
     *
     * @return Response
     *
     * @throws UnsupportedEventException
     */
    public function process($payload);
}