<?php

namespace Cubalider\Voip;

class Rate
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
    private $price;

    /**
     * @var string
     */
    private $currency;

    /**
     * @param string $country
     * @param string $network
     * @param string $price
     * @param string $currency
     */
    public function __construct(
        $country,
        $network,
        $price,
        $currency
    )
    {
        $this->country = $country;
        $this->network = $network;
        $this->price = $price;
        $this->currency = $currency;
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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
