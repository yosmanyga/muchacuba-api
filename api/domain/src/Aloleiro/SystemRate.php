<?php

namespace Muchacuba\Aloleiro;

class SystemRate implements \JsonSerializable
{
    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $network;

    /**
     * @var bool
     */
    private $favorite;

    /**
     * @var float
     */
    private $purchase;

    /**
     * @var float
     */
    private $sale;

    /**
     * @param string $country
     * @param string $network
     * @param bool   $favorite
     * @param float  $purchase
     * @param float  $sale
     */
    public function __construct(
        $country,
        $network,
        $favorite,
        $purchase,
        $sale
    )
    {
        $this->country = $country;
        $this->network = $network;
        $this->favorite = $favorite;
        $this->purchase = $purchase;
        $this->sale = $sale;
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
     * @return bool
     */
    public function isFavorite()
    {
        return $this->favorite;
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
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'country' => $this->country,
            'network' => $this->network,
            'favorite' => $this->favorite,
            'purchase' => $this->purchase,
            'sale' => $this->sale,
        ];
    }
}
