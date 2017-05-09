<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\InitConversation as DomainInitConversation;
use Symsonte\Http\Server;
use Muchacuba\Chuchuchu\CollectMessages as DomainCollectMessages;

/**
 * @di\controller({deductible: true})
 */
class InitConversation
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainInitConversation
     */
    private $initConversation;

    /**
     * @var DomainCollectMessages
     */
    private $collectMessages;

    /**
     * @param Server                 $server
     * @param DomainInitConversation $initConversation
     * @param DomainCollectMessages  $collectMessages
     */
    public function __construct(
        Server $server,
        DomainInitConversation $initConversation,
        DomainCollectMessages $collectMessages
    ) {
        $this->server = $server;
        $this->initConversation = $initConversation;
        $this->collectMessages = $collectMessages;
    }

    /**
     * @http\authorization({roles: ["chuchuchu_user"]})
     * @http\resolution({method: "POST", path: "/chuchuchu/init-conversation"})
     *
     * @param string $uniqueness
     */
    public function initConversation($uniqueness)
    {
        $post = $this->server->resolveBody();

        $conversation = $this->initConversation->init(
            $uniqueness,
            $post['receptors'],
            $post['messages']
        );

        $this->server->sendResponse([
            'conversation' => $conversation
        ]);
    }
}
