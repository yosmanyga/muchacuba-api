<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Product implements Persistable, \JsonSerializable
{
    /**
     * Sku Code
     * 
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $payload;

    /**
     * @param string $id
     * @param array $payload
     */
    public function __construct(
        $id,
        $payload
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
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'payload' => $this->payload
        ];
    }
}
