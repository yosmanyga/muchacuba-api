<?php

namespace Muchacuba\Aloleiro\Call;

class InvalidDataException extends \Exception
{
    const FIELD_TO = 'to';

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
