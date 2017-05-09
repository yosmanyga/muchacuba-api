<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\CollectClientCalls as DomainCollectClientCalls;
use Symsonte\Http\Server;
use Muchacuba\Aloleiro\PickProfile as DomainPickProfile;

/**
 * @di\controller({deductible: true})
 */
class CollectClientCalls
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
     * @var DomainCollectClientCalls
     */
    private $collectClientCalls;

    /**
     * @param Server                   $server
     * @param DomainPickProfile        $pickProfile
     * @param DomainCollectClientCalls $collectClientCalls
     */
    public function __construct(
        Server $server,
        DomainPickProfile $pickProfile,
        DomainCollectClientCalls $collectClientCalls
    ) {
        $this->server = $server;
        $this->pickProfile = $pickProfile;
        $this->collectClientCalls = $collectClientCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-client-calls"})
     *
     * @param Business $business
     */
    public function collect(Business $business)
    {
        $calls = $this->collectClientCalls->collect($business);

        $this->server->sendResponse($calls);
    }
}
