<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\CancelCall as DomainCancelCall;
use Muchacuba\Aloleiro\CollectCalls as DomainCollectCalls;
use Muchacuba\Aloleiro\NonExistentCallException;
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
     * @var DomainCollectCalls
     */
    private $collectCalls;

    /**
     * @param Server                     $server
     * @param DomainCancelCall           $cancelCall
     * @param DomainCollectCalls $collectCalls
     */
    public function __construct(
        Server $server,
        DomainCancelCall $cancelCall,
        DomainCollectCalls $collectCalls
    ) {
        $this->server = $server;
        $this->cancelCall = $cancelCall;
        $this->collectCalls = $collectCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "POST", path: "/aloleiro/cancel-call"})
     *
     * @param Business $business
     */
    public function cancel(Business $business)
    {
        $post = $this->server->resolveBody();

        try {
            $this->cancelCall->cancel(
                $business,
                $post['id']
            );
        } catch (NonExistentCallException $e) {
            // Ignore, it could be coming from an outdated view
            // TODO: Remove this once the view is updated using sockets
        }

        $calls = $this->collectCalls->collectPrepared($business);

        $this->server->sendResponse($calls);
    }
}
