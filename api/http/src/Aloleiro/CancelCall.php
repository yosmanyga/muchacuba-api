<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CancelCall as DomainCancelCall;
use Muchacuba\Aloleiro\CollectPreparedCalls as DomainCollectPreparedCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CancelCall
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCancelCall
     */
    private $cancelCall;

    /**
     * @var DomainCollectPreparedCalls
     */
    private $collectPreparedCalls;

    /**
     * @param Server                     $server
     * @param DomainCancelCall           $cancelCall
     * @param DomainCollectPreparedCalls $collectPreparedCalls
     */
    public function __construct(
        Server $server,
        DomainCancelCall $cancelCall,
        DomainCollectPreparedCalls $collectPreparedCalls
    ) {
        $this->server = $server;
        $this->cancelCall = $cancelCall;
        $this->collectPreparedCalls = $collectPreparedCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "POST", uri: "/aloleiro/cancel-call"})
     *
     * @param string $uniqueness
     */
    public function cancel($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->cancelCall->cancel(
            $uniqueness,
            $post['id']
        );

        $calls = $this->collectPreparedCalls->collect($uniqueness);

        $this->server->sendResponse($calls);
    }
}
