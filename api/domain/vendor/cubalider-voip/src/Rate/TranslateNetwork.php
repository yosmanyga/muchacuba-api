<?php

namespace Cubalider\Voip\Rate;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class TranslateNetwork
{
    /**
     * @param string $network
     *
     * @return string
     */
    public function translate($network)
    {
        return str_replace(
            ['Landline'],
            ['Fijo'],
            $network
        );
    }
}
