<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;
use Muchacuba\Aloleiro\Call\Instance;

class Call implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $business;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var Instance[]
     */
    private $instances;

    /**
     * @param string          $id
     * @param string          $business
     * @param string          $from
     * @param string          $to
     * @param Instance[]|null $instances
     */
    public function __construct(
        $id,
        $business,
        $from,
        $to,
        array $instances = []
    )
    {
        $this->id = $id;
        $this->business = $business;
        $this->from = $from;
        $this->to = $to;
        $this->instances = $instances;
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
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return Instance[]
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'business' => $this->business,
            'from' => $this->from,
            'to' => $this->to,
            'instances' => $this->instances
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->business = $data['business'];
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->instances = $data['instances'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'instances' => $this->instances,
        ];
    }
}
