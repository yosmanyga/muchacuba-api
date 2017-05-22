<?php

namespace Muchacuba\Topup;

use MongoDB\BSON\Persistable;

class Country implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $iso;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var int
     */
    private $minLength;

    /**
     * @var int
     */
    private $maxLength;

    /**
     * @var array
     */
    private $payload;

    /**
     * @param string $iso
     * @param string $name
     * @param string $prefix
     * @param string $minLength
     * @param string $maxLength
     * @param array  $payload
     */
    public function __construct(
        $iso,
        $name,
        $prefix,
        $minLength,
        $maxLength,
        $payload
    ) {
        $this->iso = $iso;
        $this->name = $name;
        $this->prefix = $prefix;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
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
            '_id' => $this->iso,
            'name' => $this->name,
            'prefix' => $this->prefix,
            'minLength' => $this->minLength,
            'maxLength' => $this->maxLength,
            'payload' => $this->payload
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->iso = $data['_id'];
        $this->name = $data['name'];
        $this->prefix = $data['prefix'];
        $this->minLength = $data['minLength'];
        $this->maxLength = $data['maxLength'];
        $this->payload = $data['payload'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'iso' => $this->iso,
            'name' => $this->name,
            'prefix' => $this->prefix,
            'payload' => $this->payload
        ];
    }
}
