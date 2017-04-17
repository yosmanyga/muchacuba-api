<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\CreateBusiness as DomainCreateBusiness;

/**
 * @di\command({deductible: true})
 */
class CreateBusiness
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCreateBusiness
     */
    private $createBusiness;

    /**
     * @param Server               $server
     * @param DomainCreateBusiness $createBusiness
     */
    public function __construct(
        Server $server,
        DomainCreateBusiness $createBusiness
    )
    {
        $this->server = $server;
        $this->createBusiness = $createBusiness;
    }

    /**
     * @cli\resolution({command: "aloleiro.create-business"})
     */
    public function create()
    {
        $input = $this->server->resolveInput();

        $this->createBusiness->create(
            $input->get('2'),
            $input->get('3')
        );
    }
}
