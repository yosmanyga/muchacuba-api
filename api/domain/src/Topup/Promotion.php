<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Promotion implements Persistable, \JsonSerializable
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
     * @var string
     */
    private $description;

    /**
     * @param string $id
     * @param string $provider
     * @param string $description
     */
    public function __construct(
        $id,
        $provider,
        $description
    ) {
        $this->id = $id;
        $this->provider = $provider;
        $this->description = $description;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'provider' => $this->provider,
            'description' => $this->description
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->provider = $data['provider'];
        $this->description = $data['description'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'provider' => $this->provider,
            'description' => $this->description
        ];
    }
}
