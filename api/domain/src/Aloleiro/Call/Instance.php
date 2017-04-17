<?php

namespace Muchacuba\Aloleiro\Call;

use MongoDB\BSON\Persistable;

class Instance implements Persistable, \JsonSerializable
{
    /**
     * @var string The provider call id
     */
    private $id;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var float
     */
    private $systemPurchase;
    
    /**
     * @var float
     */
    private $systemSale;

    /**
     * @var float
     */
    private $businessPurchase;

    /**
     * @var float
     */
    private $businessSale;
    
    /**
     * @param string     $id
     * @param int|null   $duration
     * @param int|null   $systemPurchase
     * @param float|null $systemSale
     * @param int|null   $businessPurchase
     * @param float|null $businessSale
     */
    public function __construct(
        $id,
        $duration = null,
        $systemPurchase = null,
        $systemSale = null,
        $businessPurchase = null,
        $businessSale = null
    )
    {
        $this->id = $id;
        $this->duration = $duration;
        $this->systemPurchase = $systemPurchase;
        $this->systemSale = $systemSale;
        $this->businessPurchase = $businessPurchase;
        $this->businessSale = $businessSale;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
    public function getSystemPurchase()
    {
        return $this->systemPurchase;
    }

    /**
     * @return float
     */
    public function getSystemSale()
    {
        return $this->systemSale;
    }

    /**
     * @return float
     */
    public function getBusinessPurchase()
    {
        return $this->businessPurchase;
    }

    /**
     * @return float
     */
    public function getBusinessSale()
    {
        return $this->businessSale;
    }
    
    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'duration' => $this->duration,
            'systemPurchase' => $this->systemPurchase,
            'systemSale' => $this->systemSale,
            'businessPurchase' => $this->businessPurchase,
            'businessSale' => $this->businessSale,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->duration = $data['duration'];
        $this->systemPurchase = $data['systemPurchase'];
        $this->systemSale = $data['systemSale'];
        $this->businessPurchase = $data['businessPurchase'];
        $this->businessSale = $data['businessSale'];
    }
    
    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'duration' => $this->duration,
            'systemPurchase' => $this->systemPurchase,
            'systemSale' => $this->systemSale,
            'businessPurchase' => $this->businessPurchase,
            'businessSale' => $this->businessSale,
        ];
    }
}
