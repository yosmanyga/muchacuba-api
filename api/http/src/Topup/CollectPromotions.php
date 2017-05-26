<?php

namespace Muchacuba\Http\Topup;

use Muchacuba\Topup\CollectPromotions as DomainCollectPromotions;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectPromotions
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectPromotions
     */
    private $collectPromotions;

    /**
     * @param Server                  $server
     * @param DomainCollectPromotions $collectPromotions
     */
    public function __construct(
        Server $server,
        DomainCollectPromotions $collectPromotions
    ) {
        $this->server = $server;
        $this->collectPromotions = $collectPromotions;
    }

    /**
     * @http\resolution({method: "GET", path: "/topup/collect-promotions"})
     */
    public function collect()
    {
        $promotions = $this->collectPromotions->collect();

        $this->server->sendResponse($promotions);
    }
}
