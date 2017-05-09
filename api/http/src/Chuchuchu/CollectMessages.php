<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\CollectMessages as DomainCollectMessages;
use Muchacuba\Chuchuchu\UnauthorizedException;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectMessages
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectMessages
     */
    private $collectMessages;

    /**
     * @param Server                $server
     * @param DomainCollectMessages $collectMessages
     */
    public function __construct(
        Server $server,
        DomainCollectMessages $collectMessages
    ) {
        $this->server = $server;
        $this->collectMessages = $collectMessages;
    }

    /**
     * @http\authorization({roles: ["chuchuchu_user"]})
     * @http\resolution({method: "GET", path: "/chuchuchu/collect-messages/{conversation}"})
     *
     * @param string $uniqueness
     * @param string $conversation
     */
    public function collectMessages($uniqueness, $conversation)
    {
        try {
            $messages = $this->collectMessages->collect(
                $uniqueness,
                $conversation
            );
        } catch (UnauthorizedException $e) {
            $this->server->sendResponse(null, 401);

            return;
        }

        $this->server->sendResponse($messages);
    }
}
