<?php

namespace Muchacuba\Http;

use Muchacuba\InitProfile as DomainInitProfile;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class InitProfile
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainInitProfile
     */
    private $initProfile;

    /**
     * @param Server            $server
     * @param DomainInitProfile $initProfile
     */
    public function __construct(Server $server, DomainInitProfile $initProfile)
    {
        $this->server = $server;
        $this->initProfile = $initProfile;
    }

    /**
     * @param string $uniqueness
     *
     * @http\resolution({method: "POST", uri: "/facebook/init-profile"})
     * @http\authorization({roles: ["user"]})
     */
    public function init($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->initProfile->init(
            $uniqueness,
            $post['facebook']
        );

        $this->server->sendResponse();
    }
}
