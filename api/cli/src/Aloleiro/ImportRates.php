<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\ImportRates as DomainImportRates;

/**
 * @di\command({deductible: true})
 */
class ImportRates
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainImportRates
     */
    private $importRates;

    /**
     * @param Server            $server
     * @param DomainImportRates $importRates
     */
    public function __construct(
        Server $server,
        DomainImportRates $importRates
    )
    {
        $this->server = $server;
        $this->importRates = $importRates;
    }

    /**
     * @cli\resolution({command: "aloleiro.import-rates"})
     */
    public function process()
    {
        $this->importRates->import();
    }
}
