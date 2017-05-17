<?php

namespace Muchacuba\Internauta;

class SendResult implements \JsonSerializable
{
    /**
     * @var Event[]
     */
    private $events;

    /**
     * @param Event[] $events
     */
    public function __construct(array $events)
    {
        $this->events = $events;
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
            'events' => $this->events
        ];
    }
}
