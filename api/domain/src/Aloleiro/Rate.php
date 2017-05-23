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
    private $country;

    /**
     * @var string
     */
    private $network;

    /**
     * @var string
     */
    private $price;

    /**
     * @param string $id
     * @param string $country
     * @param string $network
     * @param string $price
     */
    public function __construct(
        $id,
        $country,
        $network,
        $price
    )
    {
        $this->id = $id;
        $this->country = $country;
        $this->network = $network;
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
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getNetwork()
    {
        return $this->network;
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
            'country' => $this->country,
            'network' => $this->network,
            'price' => $this->price,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->country = $data['country'];
        $this->network = $data['network'];
        $this->price = $data['price'];
    }
}
