<?php

namespace Cubalider\Phone;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class FixNumber
{
    /**
     * @param string $number
     *
     * @return string
     *
     * @throws InvalidNumberException
     */
    public function fix($number)
    {
        $number = str_replace(['+', '-', ' '], [''], $number);

        if (!ctype_digit($number)) {
            throw new InvalidNumberException($number);
        }

        $number = '+' . $number;

        return $number;
    }
}