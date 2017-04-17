<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectClientRates as DomainCollectClientRates;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectClientRates
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectClientRates
     */
    private $collectClientRates;

    /**
     * @param Server                   $server
     * @param DomainCollectClientRates $collectClientRates
     */
    public function __construct(
        Server $server,
        DomainCollectClientRates $collectClientRates
    ) {
        $this->server = $server;
        $this->collectClientRates = $collectClientRates;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/collect-client-rates"})
     *
     * @param string $uniqueness
     */
    public function collect($uniqueness)
    {
        $rates = $this->collectClientRates->collect($uniqueness);

        $this->server->sendResponse($rates);
    }
}
