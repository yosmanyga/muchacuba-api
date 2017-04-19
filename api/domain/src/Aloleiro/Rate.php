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
    private $countryName;

    /**
     * @var string
     */
    private $countryTranslation;

    /**
     * @var float
     */
    private $countryCurrencyExchange;

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
     * @param string $countryName
     * @param string $countryTranslation
     * @param string $countryCurrencyExchange
     * @param string $type
     * @param int    $code
     * @param float  $value
     */
    public function __construct(
        $id,
        $countryName,
        $countryTranslation,
        $countryCurrencyExchange,
        $type,
        $code,
        $value
    )
    {
        $this->id = $id;
        $this->countryName = $countryName;
        $this->countryTranslation = $countryTranslation;
        $this->countryCurrencyExchange = $countryCurrencyExchange;
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
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @return string
     */
    public function getCountryTranslation()
    {
        return $this->countryTranslation;
    }

    /**
     * @return string
     */
    public function getCountryCurrencyExchange()
    {
        return $this->countryCurrencyExchange;
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
            'countryName' => $this->countryName,
            'countryTranslation' => $this->countryTranslation,
            'countryCurrencyExchange' => $this->countryCurrencyExchange,
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
        $this->countryName = $data['countryName'];
        $this->countryTranslation = $data['countryTranslation'];
        $this->countryCurrencyExchange = $data['countryCurrencyExchange'];
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
            'countryName' => $this->countryName,
            'countryTranslation' => $this->countryTranslation,
            'countryCurrencyExchange' => $this->countryCurrencyExchange,
            'type' => $this->type,
            'code' => $this->code,
            'value' => $this->value,
        ];
    }
}
