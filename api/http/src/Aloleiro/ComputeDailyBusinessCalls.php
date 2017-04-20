<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\ComputeDailyBusinessCalls as DomainComputeDailyBusinessCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ComputeDailyBusinessCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainComputeDailyBusinessCalls
     */
    private $computeDailyBusinessCalls;

    /**
     * @param Server                  $server
     * @param DomainComputeDailyBusinessCalls $computeDailyBusinessCalls
     */
    public function __construct(
        Server $server,
        DomainComputeDailyBusinessCalls $computeDailyBusinessCalls
    ) {
        $this->server = $server;
        $this->computeDailyBusinessCalls = $computeDailyBusinessCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/compute-daily-business-calls"})
     *
     * @param string $uniqueness
     */
    public function compute($uniqueness)
    {
        $stats = $this->computeDailyBusinessCalls->compute(
            $uniqueness
        );

        $this->server->sendResponse($stats);
    }
}
