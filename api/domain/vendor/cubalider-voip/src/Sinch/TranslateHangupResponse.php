<?php

namespace Cubalider\Voip\Sinch;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class TranslateHangupResponse
{
    /**
     * @return array
     */
    public function translate()
    {
        return [
            'action' => [
                'name' => 'Hangup'
            ]
        ];
    }
}