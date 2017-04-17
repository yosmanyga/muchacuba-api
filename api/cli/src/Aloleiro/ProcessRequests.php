<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Cubalider\Call\Provider\Sinch\ProcessRequests as DomainProcessRequests;

/**
 * @di\command({deductible: true})
 */
class ProcessRequests
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainProcessRequests
     */
    private $processRequests;

    /**
     * @param Server                $server
     * @param DomainProcessRequests $processRequests
     */
    public function __construct(
        Server $server,
        DomainProcessRequests $processRequests
    )
    {
        $this->server = $server;
        $this->processRequests = $processRequests;
    }

    /**
     * @cli\resolution({command: "aloleiro.process-requests"})
     */
    public function process()
    {
        $this->processRequests->process();
    }
}
