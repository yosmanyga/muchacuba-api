<?php

namespace Cubalider\Call\Provider;

use Cubalider\Call\Provider\Sinch\UnsupportedResponseException;

interface TranslateResponse
{
    /**
     * @param Response   $response
     * @param array|null $payload
     *
     * @throws UnsupportedResponseException
     */
    public function translate(Response $response, $payload = null);
}