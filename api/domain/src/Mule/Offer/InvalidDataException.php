<?php

namespace Muchacuba\Mule\Offer;

class InvalidDataException extends \Exception
{
    const FIELD_NAME = 'name';
    const FIELD_CONTACT = 'contact';
    const FIELD_ADDRESS = 'address';
    const FIELD_COORDINATES = 'coordinates';
    const FIELD_DESTINATIONS = 'destinations';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_TRIPS = 'trips';

    const TYPE_EMPTY = "empty";
    const TYPE_INVALID = "invalid";

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $field
     * @param string $type
     */
    public function __construct($field, $type)
    {
        $this->field = $field;
        $this->type = $type;

        parent::__construct(sprintf("Error \"%s\" on field \"%s\"", $this->type, $this->field));
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
