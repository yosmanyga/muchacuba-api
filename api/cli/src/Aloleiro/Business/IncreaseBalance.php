<?php

namespace Muchacuba\Cli\Aloleiro\Business;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\Business\IncreaseBalance as DomainIncreaseBalance;

/**
 * @di\command({deductible: true})
 */
class IncreaseBalance
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainIncreaseBalance
     */
    private $increaseBalance;

    /**
     * @param Server                $server
     * @param DomainIncreaseBalance $increaseBalance
     */
    public function __construct(
        Server $server,
        DomainIncreaseBalance $increaseBalance
    )
    {
        $this->server = $server;
        $this->increaseBalance = $increaseBalance;
    }

    /**
     * @cli\resolution({command: "aloleiro.business.increase-balance"})
     */
    public function process()
    {
        $input = $this->server->resolveInput();

        $this->increaseBalance->increase(
            $input->get('2'),
            $input->get('3')
        );
    }
}
