<?php

namespace Cubalider\Call\Provider;

class ConnectResponse implements Response
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