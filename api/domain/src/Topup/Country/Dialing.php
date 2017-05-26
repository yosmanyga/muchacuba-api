<?php

namespace Muchacuba\Topup\Country;

use MongoDB\BSON\Persistable;

class Dialing implements Persistable, \JsonSerializable
{
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
     * @param string $prefix
     * @param string $minLength
     * @param string $maxLength
     */
    public function __construct(
        $prefix,
        $minLength,
        $maxLength
    ) {
        $this->prefix = $prefix;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
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
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            'prefix' => $this->prefix,
            'minLength' => $this->minLength,
            'maxLength' => $this->maxLength,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->prefix = $data['prefix'];
        $this->minLength = $data['minLength'];
        $this->maxLength = $data['maxLength'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'prefix' => $this->prefix,
            'minLength' => $this->minLength,
            'maxLength' => $this->maxLength,
        ];
    }
}
