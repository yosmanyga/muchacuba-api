<?php

namespace Muchacuba\Cli\Internauta\Importing;

use Symsonte\Cli\Server;
use Muchacuba\Internauta\Importing\CountUsers as DomainCountUsers;

/**
 * @di\command({deductible: true})
 */
class CountUsers
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCountUsers
     */
    private $countUsers;

    /**
     * @param Server           $server
     * @param DomainCountUsers $countUsers
     */
    public function __construct(
        Server $server,
        DomainCountUsers $countUsers
    )
    {
        $this->server = $server;
        $this->countUsers = $countUsers;
    }

    /**
     * @cli\resolution({command: "internauta.importing.count-users"})
     */
    public function process()
    {
        $this->server->resolveOutput()->outln($this->countUsers->count());
    }
}
