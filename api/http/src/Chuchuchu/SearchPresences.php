<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\SearchPresences as DomainSearchPresences;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class SearchPresences
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainSearchPresences
     */
    private $searchPresences;

    /**
     * @param Server                $server
     * @param DomainSearchPresences $searchPresences
     */
    public function __construct(
        Server $server,
        DomainSearchPresences $searchPresences
    ) {
        $this->server = $server;
        $this->searchPresences = $searchPresences;
    }

    /**
     * @http\authorization({roles: ["chuchuchu_user"]})
     * @http\resolution({method: "POST", uri: "/chuchuchu/search-presences"})
     */
    public function search()
    {
        $post = $this->server->resolveBody();

        $presences = $this->searchPresences->search(
            $post['geoLat'],
            $post['geoLng']
        );

        $this->server->sendResponse($presences);
    }
}
