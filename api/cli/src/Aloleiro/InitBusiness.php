<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\InitBusiness as DomainInitBusiness;

/**
 * @di\command({deductible: true})
 */
class InitBusiness
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainInitBusiness
     */
    private $initBusiness;

    /**
     * @param Server             $server
     * @param DomainInitBusiness $initBusiness
     */
    public function __construct(
        Server $server,
        DomainInitBusiness $initBusiness
    )
    {
        $this->server = $server;
        $this->initBusiness = $initBusiness;
    }

    /**
     * @cli\resolution({command: "aloleiro.init-business"})
     */
    public function init()
    {
        $input = $this->server->resolveInput();

        $this->initBusiness->init(
            $input->get('2'),
            $input->get('3'),
            $input->get('4')
        );
    }
}
