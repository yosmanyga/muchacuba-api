<?php

namespace Muchacuba\Aloleiro\Call;

class ClientInstance implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

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
     * @var string
     */
    private $result;

    /**
     * @var float
     */
    private $charge;
    
    /**
     * @param string $id
     * @param int    $start
     * @param int    $end
     * @param int    $duration
     * @param string $result
     * @param int    $charge
     */
    public function __construct(
        $id,
        $start,
        $end,
        $duration,
        $result,
        $charge
    )
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->duration = $duration;
        $this->result = $result;
        $this->charge = $charge;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
     * @return string
     */
    public function getResult()
    {
        return $this->result;
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
            'id' => $this->id,
            'start' => $this->start,
            'end' => $this->end,
            'duration' => $this->duration,
            'result' => $this->result,
            'charge' => $this->charge,
        ];
    }
}
