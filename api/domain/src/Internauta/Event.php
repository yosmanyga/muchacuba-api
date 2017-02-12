<?php

namespace Muchacuba\Internauta;

class Event implements \JsonSerializable
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * @var int
     */
    private $date;

    /**
     * @param object      $object
     * @param string      $type
     * @param mixed       $payload
     * @param int|null    $date
     */
    public function __construct(
        $object,
        $type,
        $payload,
        $date = null
    ) {
        $this->type = sprintf(
            '%s%s',
            get_class($object),
            $type
                ? sprintf('.%s', $type)
                : ''
        );
        $this->payload = $payload;
        $this->date = $date ?: time();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'payload' => $this->payload,
            'date' => $this->date
        ];
    }
}
