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
     * @param bool   $favorite
     * @param float  $sale
     */
    public function __construct(
        $country,
        $network,
        $favorite,
        $sale
    )
    {
        $this->country = $country;
        $this->network = $network;
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
            'favorite' => $this->favorite,
            'sale' => $this->sale,
        ];
    }
}
