<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\ComputeDailyBusinessCalls as DomainComputeDailyBusinessCalls;
use Symsonte\Http\Server;
use Muchacuba\Aloleiro\PickProfile as DomainPickProfile;

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
     * @var DomainPickProfile
     */
    private $pickProfile;

    /**
     * @var DomainComputeDailyBusinessCalls
     */
    private $computeDailyBusinessCalls;

    /**
     * @param Server                  $server
     * @param DomainPickProfile          $pickProfile
     * @param DomainComputeDailyBusinessCalls $computeDailyBusinessCalls
     */
    public function __construct(
        Server $server,
        DomainPickProfile $pickProfile,
        DomainComputeDailyBusinessCalls $computeDailyBusinessCalls
    ) {
        $this->server = $server;
        $this->pickProfile = $pickProfile;
        $this->computeDailyBusinessCalls = $computeDailyBusinessCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", path: "/aloleiro/compute-daily-business-calls"})
     *
     * @param Business $business
     */
    public function compute(Business $business)
    {
        $stats = $this->computeDailyBusinessCalls->compute(
            $business
        );

        $this->server->sendResponse($stats);
    }
}
