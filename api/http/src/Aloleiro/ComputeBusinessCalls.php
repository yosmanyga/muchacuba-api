<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\ComputeBusinessCalls as DomainComputeBusinessCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ComputeBusinessCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainComputeBusinessCalls
     */
    private $computeBusinessCalls;

    /**
     * @param Server                            $server
     * @param DomainComputeBusinessCalls $computeBusinessCalls
     */
    public function __construct(
        Server $server,
        DomainComputeBusinessCalls $computeBusinessCalls
    ) {
        $this->server = $server;
        $this->computeBusinessCalls = $computeBusinessCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/compute-business-calls/{from}/{to}"})
     *
     * @param string $uniqueness
     * @param string $from
     * @param string $to
     */
    public function compute($uniqueness, $from, $to)
    {
        $stats = $this->computeBusinessCalls->compute(
            $uniqueness,
            $from, 
            $to
        );

        $this->server->sendResponse($stats);
    }
}
