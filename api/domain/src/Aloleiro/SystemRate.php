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
    private $type;

    /**
     * @var int
     */
    private $code;

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
     * @param string $type
     * @param int    $code
     * @param bool   $favorite
     * @param float  $purchase
     * @param float  $sale
     */
    public function __construct(
        $country,
        $type,
        $code,
        $favorite,
        $purchase,
        $sale
    )
    {
        $this->country = $country;
        $this->type = $type;
        $this->code = $code;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
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
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'country' => $this->country,
            'type' => $this->type,
            'code' => $this->code,
            'favorite' => $this->favorite,
            'purchase' => $this->purchase,
            'sale' => $this->sale,
        ];
    }
}
