<?php

namespace Muchacuba\Http\Mule;

use Muchacuba\Mule\CollectDestinations as DomainCollectDestinations;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectDestinations
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectDestinations
     */
    private $collectDestinations;

    /**
     * @param Server                    $server
     * @param DomainCollectDestinations $collectDestinations
     */
    public function __construct(
        Server $server,
        DomainCollectDestinations $collectDestinations
    ) {
        $this->server = $server;
        $this->collectDestinations = $collectDestinations;
    }

    /**
     * @http\resolution({method: "GET", uri: "/mule/collect-destinations"})
     */
    public function collect()
    {
        $destinations = $this->collectDestinations->collect();

        $this->server->sendResponse($destinations);
    }
}
