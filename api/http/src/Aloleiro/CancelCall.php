<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CancelCall as DomainCancelCall;
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
     * @param Server              $server
     * @param DomainCancelCall   $cancelCall
     */
    public function __construct(
        Server $server,
        DomainCancelCall $cancelCall
    ) {
        $this->server = $server;
        $this->cancelCall = $cancelCall;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "POST", uri: "/aloleiro/cancel-call"})
     *
     * @param string $uniqueness
     */
    public function search($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->cancelCall->cancel(
            $uniqueness,
            $post['number']
        );

        $this->server->sendResponse();
    }
}
