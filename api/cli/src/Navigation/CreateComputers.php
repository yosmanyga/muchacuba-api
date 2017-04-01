<?php

namespace Muchacuba\Cli\Navigation;

use Symsonte\Cli\Server;
use Cubalider\Navigation\CreateComputers as DomainCreateComputers;

/**
 * @di\command({deductible: true})
 */
class CreateComputers
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCreateComputers
     */
    private $createComputers;

    /**
     * @param Server                $server
     * @param DomainCreateComputers $createComputers
     */
    public function __construct(
        Server $server,
        DomainCreateComputers $createComputers
    )
    {
        $this->server = $server;
        $this->createComputers = $createComputers;
    }

    /**
     * @cli\resolution({command: "cubalider.navigation.create-computers"})
     */
    public function create()
    {
        $c = $this->createComputers->create();

        return $this->server->resolveOutput()->outln(sprintf(
                '%s %s %s',
                date('H:m:s'),
                $c,
                $c == 1 ? 'computer' : 'computers')
        );
    }
}
