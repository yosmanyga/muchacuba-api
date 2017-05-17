<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\SystemInstance;

class SystemCall implements \JsonSerializable
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
     * @var SystemInstance[]
     */
    private $instances;

    /**
     * @param string           $from
     * @param string           $to
     * @param SystemInstance[] $instances
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
     * @return SystemInstance[]
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * @return array
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
