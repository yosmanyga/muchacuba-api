<?php

namespace Cubalider\Voip\Nexmo;

use MongoDB\BSON\Persistable;

class Call implements Persistable, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $answerRequest;

    /**
     * @var array
     */
    private $answerResponse;
    
    /**
     * @var array
     */
    private $events;

    /**
     * @param string $id
     * @param array  $answerRequest
     */
    public function __construct(
        $id,
        $answerRequest
    ) {
        $this->id = $id;
        $this->answerRequest = $answerRequest;
        $this->answerResponse = null;
        $this->events = [];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getAnswerRequest()
    {
        return $this->answerRequest;
    }

    /**
     * @return array
     */
    public function getAnswerResponse()
    {
        return $this->answerResponse;
    }

    
    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'answerRequest' => $this->answerRequest,
            'answerResponse' => $this->answerResponse,
            'events' => $this->events,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->answerRequest = $data['answerRequest'];
        $this->answerResponse = $data['answerResponse'];
        $this->events = $data['events'];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'answerRequest' => $this->answerRequest,
            'answerResponse' => $this->answerResponse,
            'events' => $this->events,
        ];
    }
}
