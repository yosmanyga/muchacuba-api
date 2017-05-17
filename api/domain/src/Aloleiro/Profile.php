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
     * @var string
     */
    private $business;

    /**
     * @param string   $uniqueness
     * @param string   $business
     */
    public function __construct(
        $uniqueness,
        $business
    ) {
        $this->uniqueness = $uniqueness;
        $this->business = $business;
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
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->uniqueness,
            'business' => $this->business
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->uniqueness = $data['_id'];
        $this->business = $data['business'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'uniqueness' => $this->uniqueness,
            'business' => $this->business,
        ];
    }
}
