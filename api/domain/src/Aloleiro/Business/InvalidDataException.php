<?php

namespace Muchacuba\Aloleiro\Business;

class InvalidDataException extends \Exception
{
    const FIELD_BALANCE = 'balance';
    const FIELD_PROFIT_PERCENT = 'profitPercent';

    /**
     * @var string
     */
    private $field;

    /**
     * @param string $field
     */
    public function __construct($field)
    {
        $this->field = $field;

        parent::__construct(sprintf("Error on field \"%s\"", $field));
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
}
