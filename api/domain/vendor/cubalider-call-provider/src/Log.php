<?php

namespace Cubalider\Call\Provider;

use MongoDB\BSON\Persistable;

class Log implements Persistable, \JsonSerializable
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
     * @var mixed
     */
    private $payload;

    /**
     * @var int
     */
    private $date;

    /**
     * @param string $id
     * @param string $type
     * @param mixed  $payload
     * @param int    $date
     */
    public function __construct(
        $id,
        $type,
        $payload,
        $date
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->payload = $payload;
        $this->date = $date;
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
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'type' => $this->type,
            'payload' => $this->payload,
            'date' => $this->date
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->type = $data['type'];
        $this->payload = $data['payload'];
        $this->date = $data['date'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'payload' => $this->payload,
            'date' => $this->date,
        ];
    }
}
