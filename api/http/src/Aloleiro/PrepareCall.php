<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Call\InvalidDataException;
use Muchacuba\Aloleiro\PrepareCall as DomainPrepareCall;
use Muchacuba\Aloleiro\CollectClientCalls as DomainCollectClientCalls;
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
     * @var DomainCollectClientCalls
     */
    private $collectClientCalls;
    
    /**
     * @param Server                   $server
     * @param DomainPrepareCall        $prepareCall
     * @param DomainCollectClientCalls $collectClientCalls
     */
    public function __construct(
        Server $server,
        DomainPrepareCall $prepareCall,
        DomainCollectClientCalls $collectClientCalls
    ) {
        $this->server = $server;
        $this->prepareCall = $prepareCall;
        $this->collectClientCalls = $collectClientCalls;
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

        try {
            $this->prepareCall->prepare(
                $uniqueness,
                $post['from'],
                $post['to']
            );
        } catch (InvalidDataException $e) {
            $this->server->sendResponse([
                'field' => $e->getField()
            ], 422);

            return;
        }

        $calls = $this->collectClientCalls->collect($uniqueness);

        $this->server->sendResponse($calls);
    }
}
