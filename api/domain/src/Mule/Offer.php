<?php

namespace Muchacuba\Mule;

use MongoDB\BSON\Persistable;

class Offer implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $contact;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $geo;

    /**
     * @var string[]
     */
    private $destinations;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int[]
     */
    private $trips;

    /**
     * @param string   $id
     * @param string   $name
     * @param string   $contact
     * @param string   $address
     * @param array    $geo
     * @param string[] $destinations
     * @param string   $description
     * @param int[]    $trips
     */
    public function __construct(
        $id,
        $name,
        $contact,
        $address,
        $geo,
        array $destinations,
        $description,
        array $trips
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->contact = $contact;
        $this->address = $address;
        $this->geo = $geo;
        $this->destinations = $destinations;
        $this->description = $description;
        $this->trips = $trips;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getGeo()
    {
        return $this->geo;
    }

    /**
     * @return string[]
     */
    public function getDestinations()
    {
        return $this->destinations;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return int[]
     */
    public function getTrips()
    {
        return $this->trips;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'name' => $this->name,
            'contact' => $this->contact,
            'address' => $this->address,
            'coordinates' => $this->geo,
            'destinations' => $this->destinations,
            'description' => $this->description,
            'trips' => $this->trips,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->name = $data['name'];
        $this->contact = $data['contact'];
        $this->address = $data['address'];
        $this->geo = $data['coordinates'];
        $this->destinations = $data['destinations'];
        $this->description = $data['description'];
        $this->trips = $data['trips'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact' => $this->contact,
            'address' => $this->address,
            'coordinates' => $this->geo,
            'destinations' => $this->destinations,
            'description' => $this->description,
            'trips' => $this->trips,
        ];
    }
}
