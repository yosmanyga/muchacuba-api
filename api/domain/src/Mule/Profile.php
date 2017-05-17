<?php

namespace Muchacuba\Mule;

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
    private $offer;

    /**
     * @param string $uniqueness
     * @param string $offer
     */
    public function __construct($uniqueness, $offer)
    {
        $this->uniqueness = $uniqueness;
        $this->offer = $offer;
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
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->uniqueness,
            'offer' => $this->offer,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->uniqueness = $data['_id'];
        $this->offer = $data['offer'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'uniqueness' => $this->uniqueness,
            'offer' => $this->offer,
        ];
    }
}
