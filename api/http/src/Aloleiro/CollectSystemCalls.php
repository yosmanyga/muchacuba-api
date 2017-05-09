<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectSystemCalls as DomainCollectSystemCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectSystemCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectSystemCalls
     */
    private $collectSystemCalls;

    /**
     * @param Server                   $server
     * @param DomainCollectSystemCalls $collectSystemCalls
     */
    public function __construct(
        Server $server,
        DomainCollectSystemCalls $collectSystemCalls
    ) {
        $this->server = $server;
        $this->collectSystemCalls = $collectSystemCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_admin"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-system-calls"})
     */
    public function collect()
    {
        $calls = $this->collectSystemCalls->collect();

        $this->server->sendResponse($calls);
    }
}
