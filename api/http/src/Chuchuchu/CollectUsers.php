<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\CollectUsers as DomainCollectUsers;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectUsers
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectUsers
     */
    private $collectUsers;

    /**
     * @param Server             $server
     * @param DomainCollectUsers $collectUsers
     */
    public function __construct(
        Server $server,
        DomainCollectUsers $collectUsers
    ) {
        $this->server = $server;
        $this->collectUsers = $collectUsers;
    }

    /**
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "GET", uri: "/chuchuchu/collect-users"})
     */
    public function collect()
    {
        $users = $this->collectUsers->collect();

        $this->server->sendResponse($users);
    }
}
