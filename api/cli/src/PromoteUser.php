<?php

namespace Muchacuba\Cli;

use Symsonte\Cli\Server;
use Muchacuba\PromoteUser as DomainPromoteUser;

/**
 * @di\command({deductible: true})
 */
class PromoteUser
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPromoteUser
     */
    private $promoteUser;

    /**
     * @param Server            $server
     * @param DomainPromoteUser $promoteUser
     */
    public function __construct(
        Server $server,
        DomainPromoteUser $promoteUser
    )
    {
        $this->server = $server;
        $this->promoteUser = $promoteUser;
    }

    /**
     * @cli\resolution({command: "promote-user"})
     */
    public function promote()
    {
        $input = $this->server->resolveInput();

        $this->promoteUser->promote(
            $input->get('2'),
            $input->get('3')
        );

        $this->server->resolveOutput()->outln('Success');
    }
}
