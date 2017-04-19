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
     * @param string $id
     * @param float  $balance
     * @param int    $profitPercent
     */
    public function __construct(
        $id,
        $balance,
        $profitPercent
    ) {
        $this->id = $id;
        $this->balance = $balance;
        $this->profitPercent = $profitPercent;
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
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'balance' => $this->balance,
            'profitPercent' => $this->profitPercent,
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
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'balance' => $this->balance,
            'profitPercent' => $this->profitPercent,
        ];
    }
}
