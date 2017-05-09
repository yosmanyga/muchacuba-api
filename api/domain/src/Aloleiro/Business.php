<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Business implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var float
     */
    private $balance;

    /**
     * @var int
     */
    private $profitPercent;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @param string $id
     * @param float  $balance
     * @param int    $profitPercent
     * @param string $name
     * @param string $address
     */
    public function __construct(
        $id,
        $balance,
        $profitPercent,
        $name,
        $address
    ) {
        $this->id = $id;
        $this->balance = $balance;
        $this->profitPercent = $profitPercent;
        $this->name = $name;
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return int
     */
    public function getProfitPercent()
    {
        return $this->profitPercent;
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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'balance' => $this->balance,
            'profitPercent' => $this->profitPercent,
            'name' => $this->name,
            'address' => $this->address,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->balance = $data['balance'];
        $this->profitPercent = $data['profitPercent'];
        $this->name = $data['name'];
        $this->address = $data['address'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'balance' => (string) $this->balance,
            'profitPercent' => (string) $this->profitPercent,
            'name' => $this->name,
            'address' => $this->address,
        ];
    }
}
