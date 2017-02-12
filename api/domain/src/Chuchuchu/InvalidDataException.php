<?php

namespace Muchacuba\Chuchuchu;

class InvalidDataException extends \Exception
{
    /**
     * @var mixed
     */
    private $field;

    /**
     * @param mixed $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }
}