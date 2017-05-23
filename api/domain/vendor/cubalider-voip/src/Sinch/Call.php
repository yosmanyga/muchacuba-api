<?php

namespace Cubalider\Voip\Sinch;

use MongoDB\BSON\Persistable;

class Call implements Persistable, \JsonSerializable
{
    const STATUS_STARTED   = 'started';
    const STATUS_COMPLETED = 'completed';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @var array
     */
    private $events;

    /**
     * @param string $id
     * @param string $status
     */
    public function __construct(
        $id,
        $status
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->events = [];
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'status' => $this->status,
            'events' => $this->events,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->status = $data['status'];
        $this->events = $data['events'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'events' => $this->events,
        ];
    }
}
