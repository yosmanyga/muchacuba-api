<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\ConnectResponse;
use Cubalider\Call\Provider\Response;
use Cubalider\Call\Provider\TranslateResponse;
use Cubalider\Call\Provider\Sinch\UnsupportedResponseException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.sinch.translate_response',
 *          key: 'connect'
 *     }]
 * })
 */
class TranslateConnectResponse implements TranslateResponse
{
    /**
     * {@inheritdoc}
     */
    public function translate(Response $response, $payload = null)
    {
        if (!$response instanceof ConnectResponse) {
            throw new UnsupportedResponseException();
        }

        // Sinch sends the internal number with some spaces
        $payload['to']['endpoint'] = str_replace([' '], [''], $payload['to']['endpoint']);

        return [
            'action' => [
                'name' => 'ConnectPSTN',
                'number' => $response->getTo(),
                'maxDuration' => 3600,
                'cli' => $payload['to']['endpoint']
            ]
        ];
    }
}