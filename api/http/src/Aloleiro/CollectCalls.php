<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\CollectCalls as DomainCollectCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectCalls
     */
    private $collectCalls;

    /**
     * @param Server             $server
     * @param DomainCollectCalls $collectCalls
     */
    public function __construct(
        Server $server,
        DomainCollectCalls $collectCalls
    ) {
        $this->server = $server;
        $this->collectCalls = $collectCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-prepared-calls"})
     *
     * @param Business $business
     */
    public function collectPrepared(Business $business)
    {
        $calls = $this->collectCalls->collectPrepared($business);

        $this->server->sendResponse($calls);
    }
}
