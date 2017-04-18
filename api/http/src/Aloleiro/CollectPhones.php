<?php

namespace Muchacuba\Http\Aloleiro;

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
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/collect-phones"})
     *
     * @param string $uniqueness
     */
    public function search($uniqueness)
    {
        $phones = $this->collectPhones->collect($uniqueness);

        $this->server->sendResponse($phones);
    }
}
