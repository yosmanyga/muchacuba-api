<?php

namespace Muchacuba\Http;

use Muchacuba\InitUser as DomainInitUser;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class InitUser
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainInitUser
     */
    private $initUser;

    /**
     * @param Server         $server
     * @param DomainInitUser $initUser
     */
    public function __construct(Server $server, DomainInitUser $initUser)
    {
        $this->server = $server;
        $this->initUser = $initUser;
    }

    /**
     * @param string $uniqueness
     *
     * @http\resolution({method: "POST", uri: "/init-user"})
     * @http\authorization({roles: []})
     */
    public function init($uniqueness)
    {
        $post = $this->server->resolveBody();

        $roles = $this->initUser->init(
            $uniqueness,
            $post['facebook']
        );

        $this->server->sendResponse(['roles' => $roles]);
    }
}
