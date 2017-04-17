<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\HangupResponse;
use Cubalider\Call\Provider\Response;
use Cubalider\Call\Provider\TranslateResponse;
use Cubalider\Call\Provider\Sinch\UnsupportedResponseException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.sinch.translate_response',
 *          key: 'hangup'
 *     }]
 * })
 */
class TranslateHangupResponse implements TranslateResponse
{
    /**
     * {@inheritdoc}
     */
    public function translate(Response $response, $payload = null)
    {
        if (!$response instanceof HangupResponse) {
            throw new UnsupportedResponseException();
        }

        return [
            'action' => [
                'name' => 'Hangup'
            ]
        ];
    }
}