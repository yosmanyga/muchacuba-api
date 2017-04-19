<?php

namespace Muchacuba\Aloleiro\Call;

class ClientInstance implements \JsonSerializable
{
    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var float
     */
    private $charge;
    
    /**
     * @param int    $timestamp
     * @param int    $duration
     * @param int    $charge
     */
    public function __construct(
        $timestamp,
        $duration,
        $charge
    )
    {
        $this->timestamp = $timestamp;
        $this->duration = $duration;
        $this->charge = $charge;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return float
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'timestamp' => $this->timestamp,
            'duration' => $this->duration,
            'charge' => $this->charge,
        ];
    }
}
