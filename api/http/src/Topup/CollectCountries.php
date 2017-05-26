<?php

namespace Muchacuba\Http\Topup;

use Muchacuba\Topup\CollectCountries as DomainCollectCountries;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectCountries
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectCountries
     */
    private $collectCountries;

    /**
     * @param Server                 $server
     * @param DomainCollectCountries $collectCountries
     */
    public function __construct(
        Server $server,
        DomainCollectCountries $collectCountries
    ) {
        $this->server = $server;
        $this->collectCountries = $collectCountries;
    }

    /**
     * @http\resolution({method: "GET", path: "/topup/collect-countries"})
     */
    public function collect()
    {
        $countries = $this->collectCountries->collect();

        $this->server->sendResponse($countries);
    }
}
