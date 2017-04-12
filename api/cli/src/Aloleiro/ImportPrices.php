<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\ImportPrices as DomainImportPrices;

/**
 * @di\command({deductible: true})
 */
class ImportPrices
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainImportPrices
     */
    private $importPrices;

    /**
     * @param Server             $server
     * @param DomainImportPrices $importPrices
     */
    public function __construct(
        Server $server,
        DomainImportPrices $importPrices
    )
    {
        $this->server = $server;
        $this->importPrices = $importPrices;
    }

    /**
     * @cli\resolution({command: "aloleiro.import-prices"})
     */
    public function process()
    {
        $this->importPrices->import();
    }
}
