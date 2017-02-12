<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\InsertMessage as DomainInsertMessage;
use Symsonte\Http\Server;
use Muchacuba\Chuchuchu\CollectMessages as DomainCollectMessages;

/**
 * @di\controller({deductible: true})
 */
class InsertMessage
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainInsertMessage
     */
    private $insertMessage;

    /**
     * @var DomainCollectMessages
     */
    private $collectMessages;

    /**
     * @param Server                $server
     * @param DomainInsertMessage   $insertMessage
     * @param DomainCollectMessages $collectMessages
     */
    public function __construct(
        Server $server,
        DomainInsertMessage $insertMessage,
        DomainCollectMessages $collectMessages
    ) {
        $this->server = $server;
        $this->insertMessage = $insertMessage;
        $this->collectMessages = $collectMessages;
    }

    /**
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "POST", uri: "/chuchuchu/insert-message"})
     *
     * @param string $uniqueness
     */
    public function insertMessage($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->insertMessage->insert(
            $post['conversation'],
            $uniqueness,
            $post['content']
        );

        $contacts = $this->collectMessages->collect(
            $uniqueness,
            $post['conversation']
        );

        $this->server->sendResponse($contacts);
    }
}
