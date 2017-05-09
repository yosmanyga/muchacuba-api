<?php

namespace Muchacuba\Http\Chuchuchu\Me;

use Muchacuba\Chuchuchu\Me\ResolveTouches as DomainResolveTouches;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ResolveTouches
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainResolveTouches
     */
    private $resolveTouches;

    /**
     * @param Server               $server
     * @param DomainResolveTouches $resolveTouches
     */
    public function __construct(
        Server $server,
        DomainResolveTouches $resolveTouches
    ) {
        $this->server = $server;
        $this->resolveTouches = $resolveTouches;
    }

    /**
     * @http\authorization({roles: ["chuchuchu_user"]})
     * @http\resolution({method: "GET", path: "/chuchuchu/me/resolve-touches"})
     *
     * @param string $uniqueness
     */
    public function resolve($uniqueness)
    {
        $touches = $this->resolveTouches->collect(
            $uniqueness
        );

        $this->server->sendResponse($touches);
    }
}
