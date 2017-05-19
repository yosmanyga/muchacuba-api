<?php

namespace Cubalider\Voip;

use MongoDB\BSON\Persistable;

class Call implements Persistable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $provider;

    /**
     * @var string The original call id
     */
    private $cid;

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
     * @var float
     */
    private $cost;

    /**
     * @var string
     */
    private $currency;

    /**
     * @param string      $id
     * @param string      $provider
     * @param string      $cid
     * @param int|null    $start
     * @param int|null    $end
     * @param int|null    $duration
     * @param float|null  $cost
     * @param string|null $currency
     */
    public function __construct(
        $id,
        $provider,
        $cid,
        $start = null,
        $end = null,
        $duration = null,
        $cost = null,
        $currency = null
    )
    {
        $this->id = $id;
        $this->provider = $provider;
        $this->cid = $cid;
        $this->start = $start ?: 0;
        $this->end = $end ?: 0;
        $this->duration = $duration ?: 0;
        $this->cost = $cost ?: 0;
        $this->currency = $currency ?: null;
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
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return string
     */
    public function getCid()
    {
        return $this->cid;
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
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'provider' => $this->provider,
            'cid' => $this->cid,
            'start' => $this->start,
            'end' => $this->end,
            'duration' => $this->duration,
            'cost' => $this->cost,
            'currency' => $this->currency,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->provider = $data['provider'];
        $this->cid = $data['cid'];
        $this->start = $data['start'];
        $this->end = $data['end'];
        $this->duration = $data['duration'];
        $this->cost = $data['cost'];
        $this->currency = $data['currency'];
    }
}
