<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\CollectBusinessRates as DomainCollectBusinessRates;
use Symsonte\Http\Server;
use Muchacuba\Aloleiro\PickProfile as DomainPickProfile;

/**
 * @di\controller({deductible: true})
 */
class CollectBusinessRates
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
     * @var DomainCollectBusinessRates
     */
    private $collectBusinessRates;

    /**
     * @param Server                     $server
     * @param DomainPickProfile          $pickProfile
     * @param DomainCollectBusinessRates $collectBusinessRates
     */
    public function __construct(
        Server $server,
        DomainPickProfile $pickProfile,
        DomainCollectBusinessRates $collectBusinessRates
    ) {
        $this->server = $server;
        $this->pickProfile = $pickProfile;
        $this->collectBusinessRates = $collectBusinessRates;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-business-rates"})
     *
     * @param Business $business
     */
    public function collect(Business $business)
    {
        $rates = $this->collectBusinessRates->collect($business);

        $this->server->sendResponse($rates);
    }
}
