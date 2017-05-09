<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Rate implements Persistable
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
     * @var string
     */
    private $network;

    /**
     * @var int
     */
    private $prefix;

    /**
     * @var string
     */
    private $price;

    /**
     * @param string $id
     * @param string $countryName
     * @param string $countryTranslation
     * @param string $network
     * @param int    $prefix
     * @param string  $price
     */
    public function __construct(
        $id,
        $countryName,
        $countryTranslation,
        $network,
        $prefix,
        $price
    )
    {
        $this->id = $id;
        $this->countryName = $countryName;
        $this->countryTranslation = $countryTranslation;
        $this->network = $network;
        $this->prefix = $prefix;
        $this->price = $price;
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
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * @return int
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
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
            'network' => $this->network,
            'prefix' => $this->prefix,
            'price' => $this->price,
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
        $this->network = $data['network'];
        $this->prefix = $data['prefix'];
        $this->price = $data['price'];
    }
}
