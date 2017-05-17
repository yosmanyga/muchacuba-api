<?php

namespace Muchacuba\Cli\Aloleiro\Business;

use Muchacuba\Aloleiro\PickBusiness as DomainPickBusiness;
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
     * @var DomainPickBusiness
     */
    private $pickBusiness;

    /**
     * @var DomainIncreaseBalance
     */
    private $increaseBalance;

    /**
     * @param Server                $server
     * @param DomainPickBusiness    $pickBusiness
     * @param DomainIncreaseBalance $increaseBalance
     */
    public function __construct(
        Server $server,
        DomainPickBusiness $pickBusiness,
        DomainIncreaseBalance $increaseBalance
    )
    {
        $this->server = $server;
        $this->pickBusiness = $pickBusiness;
        $this->increaseBalance = $increaseBalance;
    }

    /**
     * @cli\resolution({command: "aloleiro.business.increase-balance"})
     */
    public function process()
    {
        $input = $this->server->resolveInput();

        $business = $this->pickBusiness->pick($input->get('2'));

        $this->increaseBalance->increase(
            $business,
            $input->get('3')
        );
    }
}
