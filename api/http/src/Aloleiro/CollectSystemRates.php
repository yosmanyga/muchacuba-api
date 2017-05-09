<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectSystemRates as DomainCollectSystemRates;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectSystemRates
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectSystemRates
     */
    private $collectSystemRates;

    /**
     * @param Server                   $server
     * @param DomainCollectSystemRates $collectSystemRates
     */
    public function __construct(
        Server $server,
        DomainCollectSystemRates $collectSystemRates
    ) {
        $this->server = $server;
        $this->collectSystemRates = $collectSystemRates;
    }

    /**
     * @http\authorization({roles: ["aloleiro_admin"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-system-rates"})
     */
    public function collect()
    {
        $rates = $this->collectSystemRates->collect();

        $this->server->sendResponse($rates);
    }
}
