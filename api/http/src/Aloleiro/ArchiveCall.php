<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\ArchiveCall as DomainArchiveCall;
use Muchacuba\Aloleiro\CollectCalls as DomainCollectCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ArchiveCall
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainArchiveCall
     */
    private $archiveCall;

    /**
     * @var DomainCollectCalls
     */
    private $collectCalls;

    /**
     * @param Server                     $server
     * @param DomainArchiveCall          $archiveCall
     * @param DomainCollectCalls $collectCalls
     */
    public function __construct(
        Server $server,
        DomainArchiveCall $archiveCall,
        DomainCollectCalls $collectCalls
    ) {
        $this->server = $server;
        $this->archiveCall = $archiveCall;
        $this->collectCalls = $collectCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "POST", path: "/aloleiro/archive-call"})
     *
     * @param Business $business
     */
    public function archive(Business $business)
    {
        $post = $this->server->resolveBody();

        $this->archiveCall->archive(
            $business,
            $post['id']
        );

        $calls = $this->collectCalls->collect($business);

        $this->server->sendResponse($calls);
    }
}
