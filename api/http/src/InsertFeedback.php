<?php

namespace Muchacuba\Http;

use Muchacuba\InsertFeedback as DomainInsertFeedback;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class InsertFeedback
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainInsertFeedback
     */
    private $insertFeedback;

    /**
     * @param Server               $server
     * @param DomainInsertFeedback $insertFeedback
     */
    public function __construct(
        Server $server,
        DomainInsertFeedback $insertFeedback
    ) {
        $this->server = $server;
        $this->insertFeedback = $insertFeedback;
    }

    /**
     * @http\resolution({method: "POST", uri: "/insert-feedback"})
     */
    public function search()
    {
        $post = $this->server->resolveBody();

        $this->insertFeedback->insert(
            null,
            $post['text']
        );

        $this->server->sendResponse();
    }
}
