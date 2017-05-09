<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\UpdateCurrencies as DomainUpdateCurrencies;

/**
 * @di\command({deductible: true})
 */
class UpdateCurrencies
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainUpdateCurrencies
     */
    private $updateCurrencies;

    /**
     * @param Server                 $server
     * @param DomainUpdateCurrencies $updateCurrencies
     */
    public function __construct(
        Server $server,
        DomainUpdateCurrencies $updateCurrencies
    )
    {
        $this->server = $server;
        $this->updateCurrencies = $updateCurrencies;
    }

    /**
     * @cli\resolution({command: "aloleiro.update-currencies"})
     */
    public function promote()
    {
        $this->updateCurrencies->update();
    }
}
