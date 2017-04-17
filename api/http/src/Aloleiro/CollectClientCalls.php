<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectClientCalls as DomainCollectClientCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectClientCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectClientCalls
     */
    private $collectClientCalls;

    /**
     * @param Server                   $server
     * @param DomainCollectClientCalls $collectClientCalls
     */
    public function __construct(
        Server $server,
        DomainCollectClientCalls $collectClientCalls
    ) {
        $this->server = $server;
        $this->collectClientCalls = $collectClientCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/collect-client-calls"})
     *
     * @param string $uniqueness
     */
    public function collect($uniqueness)
    {
        $calls = $this->collectClientCalls->collect($uniqueness);

        $this->server->sendResponse($calls);
    }
}
