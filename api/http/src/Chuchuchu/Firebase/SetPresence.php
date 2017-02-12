<?php

namespace Muchacuba\Http\Chuchuchu\Firebase;

use Muchacuba\Chuchuchu\Firebase\SetPresence as DomainSetPresence;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class SetPresence
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainSetPresence
     */
    private $setPresence;

    /**
     * @param Server            $server
     * @param DomainSetPresence $setPresence
     */
    public function __construct(
        Server $server,
        DomainSetPresence $setPresence
    ) {
        $this->server = $server;
        $this->setPresence = $setPresence;
    }

    /**
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "POST", uri: "/chuchuchu/firebase/set-presence"})
     *
     * @param string $uniqueness
     */
    public function set($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->setPresence->set(
            $uniqueness,
            $post['token']
        );

        $this->server->sendResponse();
    }
}
