<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\CollectReceptors as DomainCollectReceptors;
use Muchacuba\Chuchuchu\UnauthorizedException;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectReceptors
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectReceptors
     */
    private $collectReceptors;

    /**
     * @param Server                    $server
     * @param DomainCollectReceptors $collectReceptors
     */
    public function __construct(
        Server $server,
        DomainCollectReceptors $collectReceptors
    ) {
        $this->server = $server;
        $this->collectReceptors = $collectReceptors;
    }

    /**
     * @http\authorization({roles: ["chuchuchu_user"]})
     * @http\resolution({method: "GET", uri: "/chuchuchu/collect-receptors/{conversation}"})
     *
     * @param string $uniqueness
     * @param string $conversation
     */
    public function collectReceptors($uniqueness, $conversation)
    {
        try {
            $receptors = $this->collectReceptors->collect(
                $uniqueness,
                $conversation
            );
        } catch (UnauthorizedException $e) {
            $this->server->sendResponse(null, 401);

            return;
        }

        $this->server->sendResponse($receptors);
    }
}
