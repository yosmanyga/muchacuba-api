<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Rate implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $code;

    /**
     * @var float
     */
    private $value;

    /**
     * @param string $id
     * @param string $country
     * @param string $type
     * @param int    $code
     * @param float  $value
     */
    public function __construct(
        $id,
        $country,
        $type,
        $code,
        $value
    )
    {
        $this->id = $id;
        $this->country = $country;
        $this->type = $type;
        $this->code = $code;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'country' => $this->country,
            'type' => $this->type,
            'code' => $this->code,
            'value' => $this->value,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->country = $data['country'];
        $this->type = $data['type'];
        $this->code = $data['code'];
        $this->value = $data['value'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'country' => $this->country,
            'type' => $this->type,
            'code' => $this->code,
            'value' => $this->value,
        ];
    }
}
