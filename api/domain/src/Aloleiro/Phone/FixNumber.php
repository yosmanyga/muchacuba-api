<?php

namespace Muchacuba\Aloleiro\Phone;

use Muchacuba\Aloleiro\Business\InsufficientBalanceException;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Call\InvalidDataException;

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
     * @throws InvalidDataException
     */
    public function fix($number)
    {
        $number = str_replace(['+', '-', ' '], [''], $number);

        if (!ctype_digit($number)) {
            throw new InvalidDataException(
                InvalidDataException::FIELD_TO
            );
        }

        $number = '+' . $number;

        return $number;
    }
}