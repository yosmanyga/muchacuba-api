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
     * @var bool
     */
    private $favorite;

    /**
     * @var float
     */
    private $value;

    /**
     * @param string $id
     * @param string $country
     * @param int    $prefix
     * @param int    $code
     * @param string $type
     * @param bool   $favorite
     * @param float  $value
     */
    public function __construct(
        $id,
        $country,
        $prefix,
        $code,
        $type,
        $favorite,
        $value
    )
    {
        $this->id = $id;
        $this->country = $country;
        $this->prefix = $prefix;
        $this->code = $code;
        $this->type = $type;
        $this->favorite = $favorite;
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
     * @return bool
     */
    public function isFavorite()
    {
        return $this->favorite;
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
            'prefix' => $this->prefix,
            'code' => $this->code,
            'type' => $this->type,
            'favorite' => $this->favorite,
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
        $this->prefix = $data['prefix'];
        $this->code = $data['code'];
        $this->type = $data['type'];
        $this->favorite = $data['favorite'];
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
            'prefix' => $this->prefix,
            'code' => $this->code,
            'type' => $this->type,
            'favorite' => $this->favorite,
            'value' => $this->value,
        ];
    }
}
