<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\ComputeSystemCalls as DomainComputeSystemCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ComputeSystemCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainComputeSystemCalls
     */
    private $computeSystemCalls;

    /**
     * @param Server                   $server
     * @param DomainComputeSystemCalls $computeSystemCalls
     */
    public function __construct(
        Server $server,
        DomainComputeSystemCalls $computeSystemCalls
    ) {
        $this->server = $server;
        $this->computeSystemCalls = $computeSystemCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_admin"]})
     * @http\resolution({method: "GET", path: "/aloleiro/compute-system-calls/{from}/{to}/{by}"})
     *
     * @param string $from
     * @param string $to
     * @param string $by
     */
    public function compute($from, $to, $by)
    {
        $stats = $this->computeSystemCalls->compute(
            $from,
            $to,
            $by
        );

        $this->server->sendResponse($stats);
    }
}
