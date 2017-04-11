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
     * @var array
     */
    private $payload;

    /**
     * @param string $id
     * @param array  $payload
     */
    public function __construct(
        $id,
        array $payload
    ) {
        $this->id = $id;
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
            'payload' => $this->payload
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->payload = $data['payload'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'payload' => $this->payload,
        ];
    }
}
