<?php

namespace Muchacuba\Aloleiro\Call;

class ClientInstance implements \JsonSerializable
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $end;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var float
     */
    private $charge;
    
    /**
     * @param int    $start
     * @param int    $end
     * @param int    $duration
     * @param int    $charge
     */
    public function __construct(
        $start,
        $end,
        $duration,
        $charge
    )
    {
        $this->start = $start;
        $this->end = $end;
        $this->duration = $duration;
        $this->charge = $charge;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
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
            'start' => $this->start,
            'end' => $this->end,
            'duration' => $this->duration,
            'charge' => $this->charge,
        ];
    }
}
