<?php

namespace Cubalider\Call\Provider\Sinch;

use MongoDB\BSON\Persistable;

class Request implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $cid;

    /**
     * @param string $cid
     */
    public function __construct(
        $cid
    ) {
        $this->callId = $cid;
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
