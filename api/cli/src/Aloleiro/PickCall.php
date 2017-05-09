<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Cubalider\Call\Provider\Sinch\PickCall as DomainPickCall;

/**
 * @di\command({deductible: true})
 */
class PickCall
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPickCall
     */
    private $pickCall;

    /**
     * @param Server         $server
     * @param DomainPickCall $pickCall
     */
    public function __construct(
        Server $server,
        DomainPickCall $pickCall
    )
    {
        $this->server = $server;
        $this->pickCall = $pickCall;
    }

    /**
     * @cli\resolution({command: "aloleiro.pick-call"})
     */
    public function process()
    {
        $input = $this->server->resolveInput();

        $this->pickCall->pick($input->get(2));
    }
}
