<?php

namespace Cubalider\Voip\Sinch;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class TranslateContinueResponse
{
    /**
     * @return array
     */
    public function translate()
    {
        return [
            'action' => [
                'name' => 'Continue'
            ]
        ];
    }
}