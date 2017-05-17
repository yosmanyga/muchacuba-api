<?php

namespace Muchacuba\Http\Topup;

use Muchacuba\Topup\CollectProviders as DomainCollectProviders;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectProviders
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectProviders
     */
    private $collectProviders;

    /**
     * @param Server                $server
     * @param DomainCollectProviders $collectProviders
     */
    public function __construct(
        Server $server,
        DomainCollectProviders $collectProviders
    ) {
        $this->server = $server;
        $this->collectProviders = $collectProviders;
    }

    /**
     * @http\resolution({method: "GET", path: "/topup/collect-providers"})
     */
    public function collect()
    {
        $providers = $this->collectProviders->collect();

        $this->server->sendResponse($providers);
    }
}
