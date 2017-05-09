<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\CollectPhones as DomainCollectPhones;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectPhones
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectPhones
     */
    private $collectPhones;

    /**
     * @param Server              $server
     * @param DomainCollectPhones $collectPhones
     */
    public function __construct(
        Server $server,
        DomainCollectPhones $collectPhones
    ) {
        $this->server = $server;
        $this->collectPhones = $collectPhones;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner", "aloleiro_operator"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-phones"})
     *
     * @param Business $business
     */
    public function collect(Business $business)
    {
        $phones = $this->collectPhones->collect($business);

        $this->server->sendResponse($phones);
    }
}
