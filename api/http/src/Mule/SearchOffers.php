<?php

namespace Muchacuba\Http\Mule;

use Muchacuba\Mule\SearchOffers as DomainSearchOffers;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class SearchOffers
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainSearchOffers
     */
    private $searchOffers;

    /**
     * @param Server             $server
     * @param DomainSearchOffers $searchOffers
     */
    public function __construct(
        Server $server,
        DomainSearchOffers $searchOffers
    ) {
        $this->server = $server;
        $this->searchOffers = $searchOffers;
    }

    /**
     * @http\resolution({method: "POST", path: "/mule/search-offers"})
     */
    public function search()
    {
        file_put_contents('/tmp/' . uniqid(), print_r($_POST, true));
        return;

        $post = $this->server->resolveBody();

        $offers = $this->searchOffers->search(
            $post['coordinates'],
            null,
            $post['destination'],
            $post['from'],
            $post['to']
        );

        $this->server->sendResponse($offers);
    }
}
