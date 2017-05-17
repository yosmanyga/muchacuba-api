<?php

namespace Muchacuba\Aloleiro\Call;

class SystemInstance implements \JsonSerializable
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $end;

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
     * @param int    $start
     * @param int    $end
     * @param int    $duration
     * @param int    $purchase
     * @param float  $sale
     * @param float  $profit
     */
    public function __construct(
        $start,
        $end,
        $duration,
        $purchase,
        $sale,
        $profit
    )
    {
        $this->start = $start;
        $this->end = $end;
        $this->duration = $duration;
        $this->purchase = $purchase;
        $this->sale = $sale;
        $this->profit = $profit;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
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
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'start' => $this->start,
            'end' => $this->end,
            'duration' => $this->duration,
            'purchase' => $this->purchase,
            'sale' => $this->sale,
            'profit' => $this->profit,
        ];
    }
}
