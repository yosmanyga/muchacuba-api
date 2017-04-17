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
     * @var int
     */
    private $profitFactor;

    /**
     * @var float
     */
    private $balance;

    /**
     * @param string $id
     * @param int    $profitFactor
     * @param float  $balance
     */
    public function __construct(
        $id,
        $profitFactor,
        $balance
    ) {
        $this->id = $id;
        $this->profitFactor = $profitFactor;
        $this->balance = $balance;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getProfitFactor()
    {
        return $this->profitFactor;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'profitFactor' => $this->profitFactor,
            'balance' => $this->balance,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->profitFactor = $data['profitFactor'];
        $this->balance = $data['balance'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'profitFactor' => $this->profitFactor,
            'balance' => $this->balance,
        ];
    }
}
