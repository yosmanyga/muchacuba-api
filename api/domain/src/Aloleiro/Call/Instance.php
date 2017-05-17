<?php

namespace Muchacuba\Aloleiro\Call;

use MongoDB\BSON\Persistable;

class Instance implements Persistable, \JsonSerializable
{
    const RESULT_DID_SPEAK = 'did_speak';
    const RESULT_DID_NOT_SPEAK = 'did_not_speak';

    /**
     * @var string The provider call id
     */
    private $id;

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
     * @var string
     */
    private $result;

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
    private $systemProfit;

    /**
     * @var float
     */
    private $businessPurchase;

    /**
     * @var float
     */
    private $businessSale;

    /**
     * @var float
     */
    private $businessProfit;

    /**
     * @param string      $id
     * @param int|null    $start
     * @param int|null    $end
     * @param int|null    $duration
     * @param string|null $result
     * @param int|null    $systemPurchase
     * @param float|null  $systemSale
     * @param float|null  $systemProfit
     * @param int|null    $businessPurchase
     * @param float|null  $businessSale
     * @param float|null  $businessProfit
     */
    public function __construct(
        $id,
        $start = null,
        $end = null,
        $duration = null,
        $result = null,
        $systemPurchase = null,
        $systemSale = null,
        $systemProfit = null,
        $businessPurchase = null,
        $businessSale = null,
        $businessProfit = null
    )
    {
        $this->id = $id;
        $this->result = $result;
        $this->start = $start;
        $this->end = $end;
        $this->duration = $duration;
        $this->systemPurchase = $systemPurchase;
        $this->systemSale = $systemSale;
        $this->systemProfit = $systemProfit;
        $this->businessPurchase = $businessPurchase;
        $this->businessSale = $businessSale;
        $this->businessProfit = $businessProfit;
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
     * @return string
     */
    public function getResult()
    {
        return $this->result;
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
    public function getSystemProfit()
    {
        return $this->systemProfit;
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
     * @return float
     */
    public function getBusinessProfit()
    {
        return $this->businessProfit;
    }
    
    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            'id' => $this->id,
            'start' => $this->start,
            'end' => $this->end,
            'duration' => $this->duration,
            'result' => $this->result,
            'systemPurchase' => $this->systemPurchase,
            'systemSale' => $this->systemSale,
            'systemProfit' => $this->systemProfit,
            'businessPurchase' => $this->businessPurchase,
            'businessSale' => $this->businessSale,
            'businessProfit' => $this->businessProfit,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['id'];
        $this->start = $data['start'];
        $this->end = $data['end'];
        $this->duration = $data['duration'];
        $this->result = $data['result'];
        $this->systemPurchase = $data['systemPurchase'];
        $this->systemSale = $data['systemSale'];
        $this->systemProfit = $data['systemProfit'];
        $this->businessPurchase = $data['businessPurchase'];
        $this->businessSale = $data['businessSale'];
        $this->businessProfit = $data['businessProfit'];
    }
    
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'start' => $this->start,
            'end' => $this->end,
            'duration' => $this->duration,
            'result' => $this->result,
            'systemPurchase' => $this->systemPurchase,
            'systemSale' => $this->systemSale,
            'systemProfit' => $this->systemProfit,
            'businessPurchase' => $this->businessPurchase,
            'businessSale' => $this->businessSale,
            'businessProfit' => $this->businessProfit,
        ];
    }
}
