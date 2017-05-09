<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\FindUsersByCloseness as DomainFindUsersByCloseness;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class FindUsersByCloseness
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainFindUsersByCloseness
     */
    private $findUsersByCloseness;

    /**
     * @param Server         $server
     * @param DomainFindUsersByCloseness $findUsersByCloseness
     */
    public function __construct(Server $server, DomainFindUsersByCloseness $findUsersByCloseness)
    {
        $this->server = $server;
        $this->findUsersByCloseness = $findUsersByCloseness;
    }

    /**
     * @param string $uniqueness
     *
     * @http\resolution({method: "GET", path: "/chuchuchu/find-users-by-closeness"})
     * @http\authorization({roles: ["chuchuchu_user"]})
     */
    public function init($uniqueness)
    {
        $users = $this->findUsersByCloseness->find($uniqueness);

        $this->server->sendResponse($users);
    }
}
