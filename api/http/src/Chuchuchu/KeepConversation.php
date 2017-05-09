<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\KeepConversation as DomainKeepConversation;
use Symsonte\Http\Server;
use Muchacuba\Chuchuchu\CollectMessages as DomainCollectMessages;

/**
 * @di\controller({deductible: true})
 */
class KeepConversation
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainKeepConversation
     */
    private $keepConversation;

    /**
     * @var DomainCollectMessages
     */
    private $collectMessages;

    /**
     * @param Server                 $server
     * @param DomainKeepConversation $keepConversation
     * @param DomainCollectMessages  $collectMessages
     */
    public function __construct(
        Server $server,
        DomainKeepConversation $keepConversation,
        DomainCollectMessages $collectMessages
    ) {
        $this->server = $server;
        $this->keepConversation = $keepConversation;
        $this->collectMessages = $collectMessages;
    }

    /**
     * @http\authorization({roles: ["chuchuchu_user"]})
     * @http\resolution({method: "POST", path: "/chuchuchu/keep-conversation"})
     *
     * @param string $uniqueness
     */
    public function keep($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->keepConversation->keep(
            $uniqueness,
            $post['conversation'],
            $post['messages']
        );

        $this->server->sendResponse();
    }
}
