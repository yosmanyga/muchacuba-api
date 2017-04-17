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
     * @param int    $duration
     * @param int    $purchase
     * @param float  $sale
     */
    public function __construct(
        $duration,
        $purchase,
        $sale
    )
    {
        $this->duration = $duration;
        $this->purchase = $purchase;
        $this->sale = $sale;
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
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'duration' => $this->duration,
            'purchase' => $this->purchase,
            'sale' => $this->sale,
        ];
    }
}
