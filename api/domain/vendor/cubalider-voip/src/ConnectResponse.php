<?php

namespace Cubalider\Voip;

class ConnectResponse
{
    /**
     * @var string
     */
    private $to;

    /**
     * @param string $to
     */
    public function __construct($to)
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }
}