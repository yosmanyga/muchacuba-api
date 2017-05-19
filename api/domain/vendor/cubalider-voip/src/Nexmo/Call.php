<?php

namespace Cubalider\Voip\Nexmo;

use MongoDB\BSON\Persistable;

class Call implements Persistable, \JsonSerializable
{
    const STATUS_STARTED   = 'started';
    const STATUS_COMPLETED = 'completed';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $status;

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
     * @param string $status
     * @param array  $answerRequest
     */
    public function __construct(
        $id,
        $status,
        $answerRequest
    ) {
        $this->id = $id;
        $this->status = $status;
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
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
            'status' => $this->status,
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
        $this->status = $data['status'];
        $this->answerRequest = $data['answerRequest'];
        $this->answerResponse = $data['answerResponse'];
        $this->events = $data['events'];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'answerRequest' => $this->answerRequest,
            'answerResponse' => $this->answerResponse,
            'events' => $this->events,
        ];
    }
}
