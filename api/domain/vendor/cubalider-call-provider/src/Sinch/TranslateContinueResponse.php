<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\ContinueResponse;
use Cubalider\Call\Provider\Response;
use Cubalider\Call\Provider\TranslateResponse;
use Cubalider\Call\Provider\Sinch\UnsupportedResponseException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.sinch.translate_response',
 *          key: 'continue'
 *     }]
 * })
 */
class TranslateContinueResponse implements TranslateResponse
{
    /**
     * {@inheritdoc}
     */
    public function translate(Response $response, $payload = null)
    {
        if (!$response instanceof ContinueResponse) {
            throw new UnsupportedResponseException();
        }

        return [
            'action' => [
                'name' => 'Continue'
            ]
        ];
    }
}