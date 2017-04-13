<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Event implements Persistable, \JsonSerializable
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
     * @var array
     */
    private $payload;

    /**
     * @param string $id
     * @param string $type
     * @param array  $payload
     */
    public function __construct(
        $id,
        $type,
        array $payload
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->payload = $payload;
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
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'type' => $this->type,
            'payload' => $this->payload
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
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'payload' => $this->payload,
        ];
    }
}
