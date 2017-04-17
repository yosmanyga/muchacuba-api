<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\BusinessInstance;

class BusinessCall implements \JsonSerializable
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var BusinessInstance[]
     */
    private $instances;

    /**
     * @param string           $from
     * @param string           $to
     * @param BusinessInstance[] $instances
     */
    public function __construct(
        $from,
        $to,
        array $instances
    )
    {
        $this->from = $from;
        $this->to = $to;
        $this->instances = $instances;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return BusinessInstance[]
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'instances' => $this->instances,
        ];
    }
}
