<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\QueryCall as DomainQueryCall;
use Muchacuba\Aloleiro\CollectDailyClientCalls as DomainCollectDailyClientCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class QueryCall
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainQueryCall
     */
    private $queryCall;

    /**
     * @var DomainCollectDailyClientCalls
     */
    private $collectDailyClientCalls;

    /**
     * @param Server                        $server
     * @param DomainQueryCall               $queryCall
     * @param DomainCollectDailyClientCalls $collectDailyClientCalls
     */
    public function __construct(
        Server $server,
        DomainQueryCall $queryCall,
        DomainCollectDailyClientCalls $collectDailyClientCalls
    ) {
        $this->server = $server;
        $this->queryCall = $queryCall;
        $this->collectDailyClientCalls = $collectDailyClientCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "POST", path: "/aloleiro/query-call"})
     *
     * @param Business $business
     */
    public function query(Business $business)
    {
        $post = $this->server->resolveBody();

        $this->queryCall->query(
            $business,
            $post['id']
        );

        $calls = $this->collectDailyClientCalls->collect($business);

        $this->server->sendResponse($calls);
    }
}
