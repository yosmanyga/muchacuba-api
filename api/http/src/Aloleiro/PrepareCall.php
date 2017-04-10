<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\PrepareCall as DomainPrepareCall;
use Muchacuba\Aloleiro\CollectCalls as DomainCollectCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class PrepareCall
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPrepareCall
     */
    private $prepareCall;

    /**
     * @var DomainCollectCalls
     */
    private $collectCalls;
    
    /**
     * @param Server             $server
     * @param DomainPrepareCall  $prepareCall
     * @param DomainCollectCalls $collectCalls
     */
    public function __construct(
        Server $server,
        DomainPrepareCall $prepareCall,
        DomainCollectCalls $collectCalls
    ) {
        $this->server = $server;
        $this->prepareCall = $prepareCall;
        $this->collectCalls = $collectCalls;
    }

    /**
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "POST", uri: "/aloleiro/prepare-call"})
     *
     * @param string $uniqueness
     */
    public function search($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->prepareCall->prepare(
            $uniqueness,
            $post['from'],
            $post['to']
        );

        $this->server->sendResponse($this->collectCalls->collect($uniqueness));
    }
}
