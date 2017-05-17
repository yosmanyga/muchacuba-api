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
     * @var string
     */
    private $prefix;

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
     * @param string $prefix
     * @param bool   $favorite
     * @param float  $purchase
     * @param float  $sale
     */
    public function __construct(
        $country,
        $network,
        $prefix,
        $favorite,
        $purchase,
        $sale
    )
    {
        $this->country = $country;
        $this->network = $network;
        $this->prefix = $prefix;
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
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
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
            'prefix' => $this->prefix,
            'favorite' => $this->favorite,
            'purchase' => $this->purchase,
            'sale' => $this->sale,
        ];
    }
}
