<?php

namespace Muchacuba\Aloleiro;

class ClientRate implements \JsonSerializable
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
    private $sale;

    /**
     * @param string $country
     * @param string $network
     * @param string $prefix
     * @param bool   $favorite
     * @param float  $sale
     */
    public function __construct(
        $country,
        $network,
        $prefix,
        $favorite,
        $sale
    )
    {
        $this->country = $country;
        $this->network = $network;
        $this->prefix = $prefix;
        $this->favorite = $favorite;
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
            'sale' => $this->sale,
        ];
    }
}
