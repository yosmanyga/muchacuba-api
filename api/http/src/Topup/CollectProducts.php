<?php

namespace Muchacuba\Http\Topup;

use Muchacuba\Topup\CollectProducts as DomainCollectProducts;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectProducts
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectProducts
     */
    private $collectProducts;

    /**
     * @param Server                $server
     * @param DomainCollectProducts $collectProducts
     */
    public function __construct(
        Server $server,
        DomainCollectProducts $collectProducts
    ) {
        $this->server = $server;
        $this->collectProducts = $collectProducts;
    }

    /**
     * @http\resolution({method: "GET", path: "/topup/collect-products"})
     */
    public function collect()
    {
        $products = $this->collectProducts->collect();

        $this->server->sendResponse($products);
    }
}
