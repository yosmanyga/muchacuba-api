<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Recharge implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $to;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var string
     */
    private $status;

    /**
     * @param string $id
     * @param string $type
     * @param string $to
     * @param int    $timestamp
     * @param string $status
     */
    public function __construct(
        $id,
        $type,
        $to,
        $timestamp,
        $status
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->to = $to;
        $this->timestamp = $timestamp;
        $this->status = $status;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'type' => $this->type,
            'to' => $this->to,
            'timestamp' => $this->timestamp,
            'status' => $this->status
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->type = $data['type'];
        $this->to = $data['to'];
        $this->timestamp = $data['timestamp'];
        $this->status = $data['status'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'to' => $this->to,
            'timestamp' => $this->timestamp,
            'status' => $this->status,
        ];
    }
}
