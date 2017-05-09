<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\CollectDailyClientCalls as DomainCollectDailyClientCalls;
use Symsonte\Http\Server;
use Muchacuba\Aloleiro\PickProfile as DomainPickProfile;

/**
 * @di\controller({deductible: true})
 */
class CollectDailyClientCalls
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
     * @var DomainCollectDailyClientCalls
     */
    private $collectDailyClientCalls;

    /**
     * @param Server                        $server
     * @param DomainPickProfile             $pickProfile
     * @param DomainCollectDailyClientCalls $collectDailyClientCalls
     */
    public function __construct(
        Server $server,
        DomainPickProfile $pickProfile,
        DomainCollectDailyClientCalls $collectDailyClientCalls
    ) {
        $this->server = $server;
        $this->pickProfile = $pickProfile;
        $this->collectDailyClientCalls = $collectDailyClientCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-daily-client-calls"})
     *
     * @param Business $business
     */
    public function collect(Business $business)
    {
        $calls = $this->collectDailyClientCalls->collect($business);

        $this->server->sendResponse($calls);
    }
}
