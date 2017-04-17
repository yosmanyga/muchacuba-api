<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Phone implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $business;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $number
     * @param string $business
     * @param string $name
     */
    public function __construct(
        $number,
        $business,
        $name
    ) {
        $this->number = $number;
        $this->business = $business;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->number,
            'business' => $this->business,
            'name' => $this->name
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->number = $data['_id'];
        $this->business = $data['business'];
        $this->name = $data['name'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'number' => $this->number,
            'business' => $this->business,
            'name' => $this->name,
        ];
    }
}
