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
     * @var float
     */
    private $currencyExchange;

    /**
     * @param string $id
     * @param float  $balance
     * @param int    $profitPercent
     * @param int    $currencyExchange
     */
    public function __construct(
        $id,
        $balance,
        $profitPercent,
        $currencyExchange
    ) {
        $this->id = $id;
        $this->balance = $balance;
        $this->profitPercent = $profitPercent;
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
            'balance' => $this->balance,
            'profitPercent' => $this->profitPercent,
            'currencyExchange' => $this->currencyExchange,
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
        $this->currencyExchange = $data['currencyExchange'];
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
            'currencyExchange' => $this->currencyExchange,
        ];
    }
}
