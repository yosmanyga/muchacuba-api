<?php

namespace Muchacuba\Aloleiro\Call;

class SystemInstance implements \JsonSerializable
{
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
     * @param int    $duration
     * @param int    $purchase
     * @param float  $sale
     * @param float  $profit
     */
    public function __construct(
        $duration,
        $purchase,
        $sale,
        $profit
    )
    {
        $this->duration = $duration;
        $this->purchase = $purchase;
        $this->sale = $sale;
        $this->profit = $profit;
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
            'duration' => $this->duration,
            'purchase' => $this->purchase,
            'sale' => $this->sale,
            'profit' => $this->profit,
        ];
    }
}
