<?php

namespace Muchacuba\Aloleiro\Call;

class SystemInstance implements \JsonSerializable
{
    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var float
     */
    private $purchase;
    
    /**
     * @var float
     */
    private $sale;

    /**
     * @var float
     */
    private $profit;

    /**
     * @param int    $timestamp
     * @param int    $duration
     * @param int    $purchase
     * @param float  $sale
     * @param float  $profit
     */
    public function __construct(
        $timestamp,
        $duration,
        $purchase,
        $sale,
        $profit
    )
    {
        $this->timestamp = $timestamp;
        $this->duration = $duration;
        $this->purchase = $purchase;
        $this->sale = $sale;
        $this->profit = $profit;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return float
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @return float
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        return $this->profit;
    }
    
    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'timestamp' => $this->timestamp,
            'duration' => $this->duration,
            'purchase' => $this->purchase,
            'sale' => $this->sale,
            'profit' => $this->profit,
        ];
    }
}
