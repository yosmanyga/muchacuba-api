<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Profile implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $uniqueness;

    /**
     * @var string[]
     */
    private $phones;

    /**
     * @param string   $uniqueness
     * @param string[] $phones
     */
    public function __construct(
        $uniqueness,
        array $phones = []
    ) {
        $this->uniqueness = $uniqueness;
        $this->phones = $phones;
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
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->uniqueness,
            'phones' => $this->phones
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->uniqueness = $data['_id'];
        $this->phones = $data['phones'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'uniqueness' => $this->uniqueness,
            'phones' => $this->phones,
        ];
    }
}
