<?php

namespace Cubalider\Voip;

use MongoDB\BSON\Persistable;

class Call implements Persistable
{
    const STATUS_NONE = 'none';
    const STATUS_FIRST = 'first';
    const STATUS_SECOND = 'second';

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
     * The cost is the sum of both calls (inbound and outbound).
     *
     * @var float
     */
    private $cost;

    /**
     * The max duration between both calls (inbound and outbound).
     *
     * @var int
     */
    private $duration;

    /**
     * The min time between both calls (inbound and outbound).
     *
     * @var int
     */
    private $start;

    /**
     * The max time between both calls (inbound and outbound).
     *
     * @var int
     */
    private $end;

    /**
     * Used to know if only one call was counted or both (inbound and outbound)
     *
     * @var string
     */
    private $status;

    /**
     * @param string      $id
     * @param string      $provider
     * @param string      $cid
     * @param float|null  $cost
     * @param string|null $status
     * @param int|null    $duration
     * @param int|null    $start
     * @param int|null    $end
     */
    public function __construct(
        $id,
        $provider,
        $cid,
        $cost = null,
        $status = null,
        $duration = null,
        $start = null,
        $end = null
    )
    {
        $this->id = $id;
        $this->provider = $provider;
        $this->cid = $cid;
        $this->cost = $cost ?: 0;
        $this->status = $status ?: self::STATUS_NONE;
        $this->duration = $duration ?: 0;
        $this->start = $start ?: 0;
        $this->end = $end ?: 0;
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
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
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
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'provider' => $this->provider,
            'cid' => $this->cid,
            'cost' => $this->cost,
            'status' => $this->status,
            'duration' => $this->duration,
            'start' => $this->start,
            'end' => $this->end,
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
        $this->cost = $data['cost'];
        $this->status = $data['status'];
        $this->duration = $data['duration'];
        $this->start = $data['start'];
        $this->end = $data['end'];
    }
}
