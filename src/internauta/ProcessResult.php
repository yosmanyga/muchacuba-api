<?php

namespace Muchacuba\Internauta;

class ProcessResult implements \JsonSerializable
{
    /**
     * @var Response[]
     */
    private $responses;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * @param Response[] $responses
     * @param Event[] $events
     */
    public function __construct(array $responses, array $events)
    {
        $this->responses = $responses;
        $this->events = $events;
    }

    /**
     * @return Response[]
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'responses' => $this->responses,
            'events' => $this->events
        ];
    }
}
