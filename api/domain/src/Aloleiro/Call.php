<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Call implements Persistable, \JsonSerializable
{
    const STATUS_PREPARED = 'p';
    const STATUS_FORWARDING = 'f';
    const STATUS_ANSWERED = 'a';
    const STATUS_DISCONNECTED = 'd';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $uniqueness;

    /**
     * @var string
     */
    private $callId;

    /**
     * The Phone
     *
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $duration;

    /**
     * @param string $id
     * @param string $uniqueness
     * @param string $callId
     * @param string $from
     * @param string $to
     * @param string $status
     * @param int|null $duration
     */
    public function __construct(
        $id,
        $uniqueness,
        $callId,
        $from,
        $to,
        $status,
        $duration = null
    )
    {
        $this->id = $id;
        $this->uniqueness = $uniqueness;
        $this->callId = $callId;
        $this->from = $from;
        $this->to = $to;
        $this->status = $status;
        $this->duration = $duration;
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
    public function getUniqueness()
    {
        return $this->uniqueness;
    }

    /**
     * @return string
     */
    public function getCallId()
    {
        return $this->callId;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
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
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'uniqueness' => $this->uniqueness,
            'callId' => $this->callId,
            'from' => $this->from,
            'to' => $this->to,
            'status' => $this->status,
            'duration' => $this->duration
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->uniqueness = $data['uniqueness'];
        $this->callId = $data['callId'];
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->status = $data['status'];
        $this->duration = $data['duration'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'status' => $this->status,
            'duration' => $this->duration,
        ];
    }
}
