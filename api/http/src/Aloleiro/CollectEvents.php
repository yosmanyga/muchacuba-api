<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectEvents as DomainCollectEvents;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectEvents
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectEvents
     */
    private $collectEvents;

    /**
     * @param Server              $server
     * @param DomainCollectEvents $collectEvents
     */
    public function __construct(
        Server $server,
        DomainCollectEvents $collectEvents
    ) {
        $this->server = $server;
        $this->collectEvents = $collectEvents;
    }

    /**
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/collect-events"})
     */
    public function search()
    {
        $events = $this->collectEvents->collect();

        $this->server->sendResponse($events);
    }
}
