<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectPrices as DomainCollectPrices;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectPrices
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectPrices
     */
    private $collectPrices;

    /**
     * @param Server              $server
     * @param DomainCollectPrices $collectPrices
     */
    public function __construct(
        Server $server,
        DomainCollectPrices $collectPrices
    ) {
        $this->server = $server;
        $this->collectPrices = $collectPrices;
    }

    /**
     * @http\authorization({roles: ["admin", "seller", "operator"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/collect-prices"})
     *
     * @param string $uniqueness
     */
    public function collect($uniqueness)
    {
        $prices = $this->collectPrices->collect($uniqueness);

        $this->server->sendResponse($prices);
    }
}
