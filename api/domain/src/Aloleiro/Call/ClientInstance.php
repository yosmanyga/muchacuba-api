<?php

namespace Muchacuba\Aloleiro\Call;

class ClientInstance implements \JsonSerializable
{
    /**
     * @var int
     */
    private $duration;

    /**
     * @var float
     */
    private $charge;
    
    /**
     * @param int    $duration
     * @param int    $charge
     */
    public function __construct(
        $duration,
        $charge
    )
    {
        $this->duration = $duration;
        $this->charge = $charge;
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
            'duration' => $this->duration,
            'charge' => $this->charge,
        ];
    }
}
