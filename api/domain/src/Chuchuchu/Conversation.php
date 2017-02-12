<?php

namespace Muchacuba\Chuchuchu;

use MongoDB\BSON\Persistable;

class Conversation implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string[]
     */
    private $participants;

    /**
     * @param string   $id
     * @param string[] $participants
     */
    public function __construct(
        $id,
        array $participants
    ) {
        $this->id = $id;
        $this->participants = $participants;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'participants' => $this->participants,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->participants = $data['participants'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'participants' => $this->participants,
        ];
    }
}
