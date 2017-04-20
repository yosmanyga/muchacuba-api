<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectDailyClientCalls as DomainCollectDailyClientCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectDailyClientCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectDailyClientCalls
     */
    private $collectDailyClientCalls;

    /**
     * @param Server                        $server
     * @param DomainCollectDailyClientCalls $collectDailyClientCalls
     */
    public function __construct(
        Server $server,
        DomainCollectDailyClientCalls $collectDailyClientCalls
    ) {
        $this->server = $server;
        $this->collectDailyClientCalls = $collectDailyClientCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/collect-daily-client-calls"})
     *
     * @param string $uniqueness
     */
    public function collect($uniqueness)
    {
        $calls = $this->collectDailyClientCalls->collect($uniqueness);

        $this->server->sendResponse($calls);
    }
}
