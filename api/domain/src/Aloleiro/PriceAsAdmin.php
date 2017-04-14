<?php

namespace Muchacuba\Aloleiro;

class PriceAsAdmin implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $country;

    /**
     * @var int
     */
    private $prefix;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $favorite;

    /**
     * @var float
     */
    private $purchaseValue;

    /**
     * @var float
     */
    private $saleValue;

    /**
     * @param string $id
     * @param string $country
     * @param int    $prefix
     * @param int    $code
     * @param string $type
     * @param bool   $favorite
     * @param float  $purchaseValue
     * @param float  $saleValue
     */
    public function __construct(
        $id,
        $country,
        $prefix,
        $code,
        $type,
        $favorite,
        $purchaseValue,
        $saleValue
    )
    {
        $this->id = $id;
        $this->country = $country;
        $this->prefix = $prefix;
        $this->code = $code;
        $this->type = $type;
        $this->favorite = $favorite;
        $this->purchaseValue = $purchaseValue;
        $this->saleValue = $saleValue;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return int
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
    public function getPurchaseValue(): float
    {
        return $this->purchaseValue;
    }

    /**
     * @return float
     */
    public function getSaleValue()
    {
        return $this->saleValue;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            '_id' => $this->id,
            'country' => $this->country,
            'prefix' => $this->prefix,
            'code' => $this->code,
            'type' => $this->type,
            'favorite' => $this->favorite,
            'purchaseValue' => $this->purchaseValue,
            'saleValue' => $this->saleValue,
        ];
    }
}
