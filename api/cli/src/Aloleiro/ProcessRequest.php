<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\ProcessRequest as DomainProcessRequest;

/**
 * @di\command({deductible: true})
 */
class ProcessRequest
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainProcessRequest
     */
    private $processRequest;

    /**
     * @param Server               $server
     * @param DomainProcessRequest $processRequest
     */
    public function __construct(
        Server $server,
        DomainProcessRequest $processRequest
    )
    {
        $this->server = $server;
        $this->processRequest = $processRequest;
    }

    /**
     * @cli\resolution({command: "aloleiro.process-request"})
     */
    public function process()
    {
        $this->processRequest->process();
    }
}
