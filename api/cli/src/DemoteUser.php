<?php

namespace Muchacuba\Cli;

use Symsonte\Cli\Server;
use Muchacuba\DemoteUser as DomainDemoteUser;

/**
 * @di\command({deductible: true})
 */
class DemoteUser
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainDemoteUser
     */
    private $demoteUser;

    /**
     * @param Server            $server
     * @param DomainDemoteUser $demoteUser
     */
    public function __construct(
        Server $server,
        DomainDemoteUser $demoteUser
    )
    {
        $this->server = $server;
        $this->demoteUser = $demoteUser;
    }

    /**
     * @cli\resolution({command: "demote-user"})
     */
    public function demote()
    {
        $input = $this->server->resolveInput();

        $this->demoteUser->demote(
            $input->get('2'),
            $input->get('3')
        );

        $this->server->resolveOutput()->outln('Success');
    }
}
