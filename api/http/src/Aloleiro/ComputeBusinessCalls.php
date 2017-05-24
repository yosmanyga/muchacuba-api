<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\ComputeBusinessCalls as DomainComputeBusinessCalls;
use Symsonte\Http\Server;
use Muchacuba\Aloleiro\PickProfile as DomainPickProfile;

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
     * @var DomainPickProfile
     */
    private $pickProfile;

    /**
     * @var DomainComputeBusinessCalls
     */
    private $computeBusinessCalls;

    /**
     * @param Server                            $server
     * @param DomainPickProfile          $pickProfile
     * @param DomainComputeBusinessCalls $computeBusinessCalls
     */
    public function __construct(
        Server $server,
        DomainPickProfile $pickProfile,
        DomainComputeBusinessCalls $computeBusinessCalls
    ) {
        $this->server = $server;
        $this->pickProfile = $pickProfile;
        $this->computeBusinessCalls = $computeBusinessCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "GET", path: "/aloleiro/compute-business-calls/{from}/{to}/{by}"})
     *
     * @param Business $business
     * @param string $from
     * @param string $to
     * @param string $by
     */
    public function compute(Business $business, $from, $to, $by)
    {
        $stats = $this->computeBusinessCalls->compute(
            $business,
            $from, 
            $to,
            $by
        );

        $this->server->sendResponse($stats);
    }
}
