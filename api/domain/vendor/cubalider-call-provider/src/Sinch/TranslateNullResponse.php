<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\NullResponse;
use Cubalider\Call\Provider\Response;
use Cubalider\Call\Provider\TranslateResponse;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.sinch.translate_response',
 *          key: 'null'
 *     }]
 * })
 */
class TranslateNullResponse implements TranslateResponse
{
    /**
     * {@inheritdoc}
     */
    public function translate(Response $response, $payload = null)
    {
        if (!$response instanceof NullResponse) {
            throw new UnsupportedResponseException();
        }

        return null;
    }
}