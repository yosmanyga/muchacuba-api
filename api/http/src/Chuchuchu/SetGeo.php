<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\User\SetGeo as DomainSetGeo;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class SetGeo
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainSetGeo
     */
    private $setGeo;

    /**
     * @param Server       $server
     * @param DomainSetGeo $setGeo
     */
    public function __construct(
        Server $server,
        DomainSetGeo $setGeo
    ) {
        $this->server = $server;
        $this->setGeo = $setGeo;
    }

    /**
     * @http\authorization({roles: ["chuchuchu_user"]})
     * @http\resolution({method: "POST", path: "/chuchuchu/set-geo"})
     *
     * @param string $uniqueness
     */
    public function set($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->setGeo->set(
            $uniqueness,
            $post['lat'],
            $post['lng']
        );

        $this->server->sendResponse();
    }
}
