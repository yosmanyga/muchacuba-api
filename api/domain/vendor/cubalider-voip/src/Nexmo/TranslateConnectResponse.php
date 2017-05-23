<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\ConnectResponse;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class TranslateConnectResponse
{
    /**
     * @param ConnectResponse $response
     * @param string          $from
     *
     * @return array
     */
    public function translate(ConnectResponse $response, $from)
    {
        return [
            /* Disabled for now, because it removes the ringing sound
            [
                'action' => 'talk',
                'text' => 'Por favor, espere mientras le comunicamos',
                'voiceName' => 'Conchita'
            ],
            */
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