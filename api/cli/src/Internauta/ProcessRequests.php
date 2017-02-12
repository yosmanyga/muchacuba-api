<?php

namespace Muchacuba\Cli\Internauta;

use Symsonte\Cli\Server;
use Muchacuba\Internauta\ProcessRequests as DomainProcessRequests;

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
     * @cli\resolution({command: "internauta.process-requests"})
     */
    public function process()
    {
        try {
            $this->processRequests->process();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
