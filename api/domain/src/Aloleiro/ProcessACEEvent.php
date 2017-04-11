<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessACEEvent
{
    /**
     * @return array
     */
    public function process()
    {
        return [
            'action' => [
                'name' => 'Continue'
            ]
        ];
    }
}