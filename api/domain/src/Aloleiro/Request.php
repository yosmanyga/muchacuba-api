<?php

namespace Muchacuba\Aloleiro;

use MongoDB\BSON\Persistable;

class Request implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $callId;

    /**
     * @param string $callId
     */
    public function __construct(
        $callId
    ) {
        $this->callId = $callId;
    }

    /**
     * @return string
     */
    public function getCallId()
    {
        return $this->callId;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->callId
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->callId = $data['_id'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'callId' => $this->callId
        ];
    }
}
