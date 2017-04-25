<?php

namespace Muchacuba\Http\Mule;

use Muchacuba\Mule\CountOffers as DomainCountOffers;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CountOffers
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCountOffers
     */
    private $countOffers;

    /**
     * @param Server             $server
     * @param DomainCountOffers $countOffers
     */
    public function __construct(
        Server $server,
        DomainCountOffers $countOffers
    ) {
        $this->server = $server;
        $this->countOffers = $countOffers;
    }

    /**
     * @http\resolution({method: "GET", uri: "/mule/count-offers"})
     * @http\authorization({roles: ["mule_admin"]})
     */
    public function count()
    {
        $this->server->sendResponse($this->countOffers->count());
    }
}
