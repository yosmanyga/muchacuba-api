<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\CollectConversations as DomainCollectConversations;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectConversations
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectConversations
     */
    private $collectConversations;

    /**
     * @param Server                     $server
     * @param DomainCollectConversations $collectConversations
     */
    public function __construct(
        Server $server,
        DomainCollectConversations $collectConversations
    ) {
        $this->server = $server;
        $this->collectConversations = $collectConversations;
    }

    /**
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "GET", uri: "/chuchuchu/collect-conversations"})
     *
     * @param string $uniqueness
     */
    public function collect($uniqueness)
    {
        $conversations = $this->collectConversations->collect(
            $uniqueness
        );

        $this->server->sendResponse($conversations);
    }
}
