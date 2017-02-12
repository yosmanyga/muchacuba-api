<?php

namespace Muchacuba\Cli\Internauta\Importing\Cubamessenger;

use Symsonte\Cli\Server;
use Muchacuba\Internauta\Importing\Cubamessenger\ImportUsers as DomainImportUsers;

/**
 * @di\command({deductible: true})
 */
class ImportUsers
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainImportUsers
     */
    private $importUsers;

    /**
     * @param Server            $server
     * @param DomainImportUsers $importUsers
     */
    public function __construct(
        Server $server,
        DomainImportUsers $importUsers
    )
    {
        $this->server = $server;
        $this->importUsers = $importUsers;
    }

    /**
     * @cli\resolution({command: "internauta.importing.cubamessenger.import-users"})
     */
    public function process()
    {
        $c = $this->importUsers->import();

        return $this->server->resolveOutput()->outln($c);
    }
}
