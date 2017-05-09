<?php

namespace Muchacuba\Http;

use Muchacuba\InitFacebookUser as DomainInitFacebookUser;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class InitFacebookUser
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainInitFacebookUser
     */
    private $initFacebookUser;

    /**
     * @param Server                 $server
     * @param DomainInitFacebookUser $initFacebookUser
     */
    public function __construct(Server $server, DomainInitFacebookUser $initFacebookUser)
    {
        $this->server = $server;
        $this->initFacebookUser = $initFacebookUser;
    }

    /**
     * @param string $uniqueness
     *
     * @http\resolution({method: "POST", path: "/init-facebook-user"})
     * @http\authorization({roles: []})
     */
    public function init($uniqueness)
    {
        $post = $this->server->resolveBody();

        $roles = $this->initFacebookUser->init(
            $uniqueness,
            $post['id'],
            $post['name'],
            $post['email'],
            $post['picture']
        );

        $this->server->sendResponse(['roles' => $roles]);
    }
}
