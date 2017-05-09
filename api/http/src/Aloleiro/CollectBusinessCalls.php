<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\CollectBusinessCalls as DomainCollectBusinessCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectBusinessCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectBusinessCalls
     */
    private $collectBusinessCalls;

    /**
     * @param Server                     $server
     * @param DomainCollectBusinessCalls $collectBusinessCalls
     */
    public function __construct(
        Server $server,
        DomainCollectBusinessCalls $collectBusinessCalls
    ) {
        $this->server = $server;
        $this->collectBusinessCalls = $collectBusinessCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-business-calls"})
     *
     * @param Business $business
     */
    public function collect(Business $business)
    {
        $calls = $this->collectBusinessCalls->collect($business);

        $this->server->sendResponse($calls);
    }
}
