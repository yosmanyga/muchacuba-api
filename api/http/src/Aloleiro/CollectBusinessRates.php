<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectBusinessRates as DomainCollectBusinessRates;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectBusinessRates
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectBusinessRates
     */
    private $collectBusinessRates;

    /**
     * @param Server                     $server
     * @param DomainCollectBusinessRates $collectBusinessRates
     */
    public function __construct(
        Server $server,
        DomainCollectBusinessRates $collectBusinessRates
    ) {
        $this->server = $server;
        $this->collectBusinessRates = $collectBusinessRates;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/collect-business-rates"})
     *
     * @param string $uniqueness
     */
    public function collect($uniqueness)
    {
        $rates = $this->collectBusinessRates->collect($uniqueness);

        $this->server->sendResponse($rates);
    }
}
