<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\ComputeMonthlySystemCalls as DomainComputeMonthlySystemCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ComputeMonthlySystemCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainComputeMonthlySystemCalls
     */
    private $computeMonthlySystemCalls;

    /**
     * @param Server                          $server
     * @param DomainComputeMonthlySystemCalls $computeMonthlySystemCalls
     */
    public function __construct(
        Server $server,
        DomainComputeMonthlySystemCalls $computeMonthlySystemCalls
    ) {
        $this->server = $server;
        $this->computeMonthlySystemCalls = $computeMonthlySystemCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_admin"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/compute-monthly-system-calls"})
     *
     * @param string $uniqueness
     */
    public function compute($uniqueness)
    {
        $stats = $this->computeMonthlySystemCalls->compute(
            $uniqueness
        );

        $this->server->sendResponse($stats);
    }
}
