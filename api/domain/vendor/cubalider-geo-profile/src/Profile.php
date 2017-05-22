<?php

namespace Cubalider\Geo;

use MongoDB\BSON\Persistable;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 */
class Profile implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $uniqueness;

    /**
     * @var string
     */
    private $lat;

    /**
     * @var string
     */
    private $lng;

    /**
     * @param string $uniqueness
     * @param string $lat
     * @param string $lng
     */
    public function __construct($uniqueness, $lat, $lng)
    {
        $this->uniqueness = $uniqueness;
        $this->lat = $lat;
        $this->lng = $lng;
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
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id'   => $this->uniqueness,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->uniqueness = $data['_id'];
        $this->lat = $data['lat'];
        $this->lng = $data['lng'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'uniqueness'    => $this->uniqueness,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
