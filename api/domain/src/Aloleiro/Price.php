<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Price implements Persistable, \JsonSerializable
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
     * @var int
     */
    private $prefix;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $type;

    /**
     * @var float
     */
    private $value;

    /**
     * @var bool
     */
    private $favorite;

    /**
     * @param string $id
     * @param string $country
     * @param int    $prefix
     * @param int    $code
     * @param string $type
     * @param float  $value
     * @param bool   $favorite
     */
    public function __construct(
        $id,
        $country,
        $prefix,
        $code,
        $type,
        $value,
        $favorite
    )
    {
        $this->id = $id;
        $this->country = $country;
        $this->prefix = $prefix;
        $this->code = $code;
        $this->type = $type;
        $this->value = $value;
        $this->favorite = $favorite;
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
     * @return int
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isFavorite()
    {
        return $this->favorite;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'country' => $this->country,
            'prefix' => $this->prefix,
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'favorite' => $this->favorite,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->country = $data['country'];
        $this->prefix = $data['prefix'];
        $this->code = $data['code'];
        $this->type = $data['type'];
        $this->value = $data['value'];
        $this->favorite = $data['favorite'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'country' => $this->country,
            'prefix' => $this->prefix,
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'favorite' => $this->favorite,
        ];
    }
}
