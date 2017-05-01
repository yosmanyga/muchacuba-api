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
     * @param Server                          $server
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
     * @http\resolution({method: "GET", uri: "/aloleiro/compute-system-calls/{from}/{to}"})
     *
     * @param string $from
     * @param string $to
     */
    public function compute($from, $to)
    {
        $stats = $this->computeSystemCalls->compute(
            $from,
            $to
        );

        $this->server->sendResponse($stats);
    }
}
