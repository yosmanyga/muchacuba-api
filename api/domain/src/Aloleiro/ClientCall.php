<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ClientInstance;

class ClientCall implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var ClientInstance[]
     */
    private $instances;

    /**
     * @param string           $id
     * @param string           $from
     * @param string           $to
     * @param ClientInstance[] $instances
     */
    public function __construct(
        $id,
        $from,
        $to,
        array $instances
    )
    {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->instances = $instances;
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
     * @return ClientInstance[]
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
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'instances' => $this->instances,
        ];
    }
}
