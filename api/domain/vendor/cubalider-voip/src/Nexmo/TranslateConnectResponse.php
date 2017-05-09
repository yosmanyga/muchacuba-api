<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\ConnectResponse;
use Cubalider\Voip\TranslateResponse;
use Cubalider\Voip\UnsupportedResponseException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: ['cubalider.voip.nexmo.translate_response']
 * })
 */
class TranslateConnectResponse implements TranslateResponse
{
    /**
     * {@inheritdoc}
     */
    public function translate($response, $cid, $from)
    {
        if (!$response instanceof ConnectResponse) {
            throw new UnsupportedResponseException();
        }

        return [
            [
                'action' => 'talk',
                'text' => 'Por favor, espere mientras le comunicamos',
                'voiceName' => 'Conchita'
            ],
            [
                'action' => 'connect',
                'from' => $from,
                'endpoint' => [
                    [
                        'type' => 'phone',
                        'number' => $response->getTo()
                    ]
                ]
            ]
        ];
    }
}