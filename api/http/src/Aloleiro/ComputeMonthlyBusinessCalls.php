<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\ComputeMonthlyBusinessCalls as DomainComputeMonthlyBusinessCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ComputeMonthlyBusinessCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainComputeMonthlyBusinessCalls
     */
    private $computeMonthlyBusinessCalls;

    /**
     * @param Server                            $server
     * @param DomainComputeMonthlyBusinessCalls $computeMonthlyBusinessCalls
     */
    public function __construct(
        Server $server,
        DomainComputeMonthlyBusinessCalls $computeMonthlyBusinessCalls
    ) {
        $this->server = $server;
        $this->computeMonthlyBusinessCalls = $computeMonthlyBusinessCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/compute-monthly-business-calls"})
     *
     * @param string $uniqueness
     */
    public function compute($uniqueness)
    {
        $stats = $this->computeMonthlyBusinessCalls->compute(
            $uniqueness
        );

        $this->server->sendResponse($stats);
    }
}
