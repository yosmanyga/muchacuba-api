<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\UpdateVenezuelanCurrencyExchange as DomainUpdateVenezuelanCurrencyExchange;

/**
 * @di\command({deductible: true})
 */
class UpdateVenezuelanCurrencyExchange
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainUpdateVenezuelanCurrencyExchange
     */
    private $updateVenezuelanCurrencyExchange;

    /**
     * @param Server                                 $server
     * @param DomainUpdateVenezuelanCurrencyExchange $updateVenezuelanCurrencyExchange
     */
    public function __construct(
        Server $server,
        DomainUpdateVenezuelanCurrencyExchange $updateVenezuelanCurrencyExchange
    )
    {
        $this->server = $server;
        $this->updateVenezuelanCurrencyExchange = $updateVenezuelanCurrencyExchange;
    }

    /**
     * @cli\resolution({command: "aloleiro.update-venezuelan-currency-exchange"})
     */
    public function promote()
    {
        $this->updateVenezuelanCurrencyExchange->update();
    }
}
