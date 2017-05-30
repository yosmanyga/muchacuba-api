<?php

namespace Cubalider\Phone;

class InvalidNumberException extends \Exception
{
    /**
     * @var string
     */
    private $number;

    /**
     * @param string $number
     */
    public function __construct($number)
    {
        $this->number = $number;

        parent::__construct(sprintf("Number \"%s\" is incorrect.", $number));
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }
}
