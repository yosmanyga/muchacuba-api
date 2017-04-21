<?php

namespace Muchacuba\Aloleiro\Phone;

class InvalidDataException extends \Exception
{
    const FIELD_NUMBER = 'number';
    const FIELD_NAME = 'name';

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
