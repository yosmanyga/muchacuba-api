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
    private $name;

    /**
     * @param string   $number
     * @param string   $name
     */
    public function __construct(
        $number,
        $name
    ) {
        $this->number = $number;
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
            'name' => $this->name
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->number = $data['_id'];
        $this->name = $data['name'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'number' => $this->number,
            'name' => $this->name,
        ];
    }
}
