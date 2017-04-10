<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Call implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $uniqueness;

    /**
     * The Phone
     *
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @param string $id
     * @param string $uniqueness
     * @param string $from
     * @param string $to
     */
    public function __construct($id, $uniqueness, $from, $to)
    {
        $this->id = $id;
        $this->uniqueness = $uniqueness;
        $this->from = $from;
        $this->to = $to;
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
    public function getUniqueness()
    {
        return $this->uniqueness;
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
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'uniqueness' => $this->uniqueness,
            'from' => $this->from,
            'to' => $this->to
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->uniqueness = $data['uniqueness'];
        $this->from = $data['from'];
        $this->to = $data['to'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'uniqueness' => $this->uniqueness,
            'from' => $this->from,
            'to' => $this->to,
        ];
    }
}
