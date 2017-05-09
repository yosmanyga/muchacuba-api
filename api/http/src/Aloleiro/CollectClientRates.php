<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\CollectClientRates as DomainCollectClientRates;
use Symsonte\Http\Server;
use Muchacuba\Aloleiro\PickProfile as DomainPickProfile;

/**
 * @di\controller({deductible: true})
 */
class CollectClientRates
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
     * @var DomainCollectClientRates
     */
    private $collectClientRates;

    /**
     * @param Server                   $server
     * @param DomainPickProfile        $pickProfile
     * @param DomainCollectClientRates $collectClientRates
     */
    public function __construct(
        Server $server,
        DomainPickProfile $pickProfile,
        DomainCollectClientRates $collectClientRates
    ) {
        $this->server = $server;
        $this->pickProfile = $pickProfile;
        $this->collectClientRates = $collectClientRates;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-client-rates"})
     *
     * @param Business $business
     */
    public function collect(Business $business)
    {
        $rates = $this->collectClientRates->collect($business);

        $this->server->sendResponse($rates);
    }
}
