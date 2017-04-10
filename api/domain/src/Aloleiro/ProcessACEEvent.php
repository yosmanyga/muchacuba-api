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
     * @return string
     */
    public function process()
    {
        return <<<EOF
{
    "name" : "Continue"
}
EOF;
    }
}