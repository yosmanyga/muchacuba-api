<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectPreparedCalls as DomainCollectPreparedCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectPreparedCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectPreparedCalls
     */
    private $collectPreparedCalls;

    /**
     * @param Server                     $server
     * @param DomainCollectPreparedCalls $collectPreparedCalls
     */
    public function __construct(
        Server $server,
        DomainCollectPreparedCalls $collectPreparedCalls
    ) {
        $this->server = $server;
        $this->collectPreparedCalls = $collectPreparedCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/collect-prepared-calls"})
     *
     * @param string $uniqueness
     */
    public function collect($uniqueness)
    {
        $calls = $this->collectPreparedCalls->collect($uniqueness);

        $this->server->sendResponse($calls);
    }
}
