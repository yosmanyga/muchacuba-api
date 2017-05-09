<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectBusinesses as DomainCollectBusinesses;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectBusinesses
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectBusinesses
     */
    private $collectBusinesses;

    /**
     * @param Server              $server
     * @param DomainCollectBusinesses $collectBusinesses
     */
    public function __construct(
        Server $server,
        DomainCollectBusinesses $collectBusinesses
    ) {
        $this->server = $server;
        $this->collectBusinesses = $collectBusinesses;
    }

    /**
     * @http\authorization({roles: ["aloleiro_admin"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-businesses"})
     */
    public function collect()
    {
        $businesses = $this->collectBusinesses->collect();

        $this->server->sendResponse($businesses);
    }
}
