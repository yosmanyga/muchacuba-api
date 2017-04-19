<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Country implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $translation;

    /**
     * @var float
     */
    private $currencyExchange;

    /**
     * @param string $id
     * @param string $name
     * @param string $translation
     * @param float  $currencyExchange
     */
    public function __construct(
        $id,
        $name,
        string $translation,
        $currencyExchange
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->translation = $translation;
        $this->currencyExchange = $currencyExchange;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * @return float
     */
    public function getCurrencyExchange()
    {
        return $this->currencyExchange;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'name' => $this->name,
            'translation' => $this->translation,
            'currencyExchange' => $this->currencyExchange
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->name = $data['name'];
        $this->translation = $data['translation'];
        $this->currencyExchange = $data['currencyExchange'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'translation' => $this->translation,
            'currencyExchange' => $this->currencyExchange,
        ];
    }
}
