<?php

namespace Muchacuba\Http\Aloleiro;

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
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/collect-calls"})
     *
     * @param string $uniqueness
     */
    public function collect($uniqueness)
    {
        $calls = $this->collectCalls->collect($uniqueness);

        $this->server->sendResponse($calls);
    }
}
