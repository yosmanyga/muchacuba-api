<?php

namespace Cubalider\Voip\Sinch;

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
            'action' => [
                'name' => 'ConnectPSTN',
                'number' => $response->getTo(),
                'maxDuration' => 3600,
                'cli' => $from
            ]
        ];
    }
}