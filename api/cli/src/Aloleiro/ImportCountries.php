<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\ImportCountries as DomainImportCountries;

/**
 * @di\command({deductible: true})
 */
class ImportCountries
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainImportCountries
     */
    private $importCountries;

    /**
     * @param Server             $server
     * @param DomainImportCountries $importCountries
     */
    public function __construct(
        Server $server,
        DomainImportCountries $importCountries
    )
    {
        $this->server = $server;
        $this->importCountries = $importCountries;
    }

    /**
     * @cli\resolution({command: "aloleiro.import-countries"})
     */
    public function process()
    {
        $this->importCountries->import();
    }
}
