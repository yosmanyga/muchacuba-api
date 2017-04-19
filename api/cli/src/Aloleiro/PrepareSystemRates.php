<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\PrepareSystemRates as DomainPrepareSystemRates;

/**
 * @di\command({deductible: true})
 */
class PrepareSystemRates
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPrepareSystemRates
     */
    private $prepareSystemRates;

    /**
     * @param Server                   $server
     * @param DomainPrepareSystemRates $prepareSystemRates
     */
    public function __construct(
        Server $server,
        DomainPrepareSystemRates $prepareSystemRates
    )
    {
        $this->server = $server;
        $this->prepareSystemRates = $prepareSystemRates;
    }

    /**
     * @cli\resolution({command: "aloleiro.prepare-system-rates"})
     */
    public function process()
    {
        $this->prepareSystemRates->prepare();
    }
}
