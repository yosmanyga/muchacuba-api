<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\CreateAdminApproval as DomainCreateAdminApproval;

/**
 * @di\command({deductible: true})
 */
class CreateAdminApproval
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCreateAdminApproval
     */
    private $createAdminApproval;

    /**
     * @param Server                    $server
     * @param DomainCreateAdminApproval $createAdminApproval
     */
    public function __construct(
        Server $server,
        DomainCreateAdminApproval $createAdminApproval
    )
    {
        $this->server = $server;
        $this->createAdminApproval = $createAdminApproval;
    }

    /**
     * @cli\resolution({command: "aloleiro.create-admin-approval"})
     */
    public function create()
    {
        $input = $this->server->resolveInput();

        $this->createAdminApproval->create(
            $input->get('2'),
            $input->get('3')
        );

        $this->server->resolveOutput()->outln('Success');
    }
}
