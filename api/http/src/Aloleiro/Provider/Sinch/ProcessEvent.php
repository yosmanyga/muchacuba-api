<?php

namespace Muchacuba\Http\Aloleiro\Provider\Sinch;

use Cubalider\Voip\Sinch\ProcessEvent as DomainProcessEvent;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ProcessEvent
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainProcessEvent
     */
    private $processEvent;

    /**
     * @param Server              $server
     * @param DomainProcessEvent  $processEvent
     */
    public function __construct(
        Server $server,
        DomainProcessEvent $processEvent
    ) {
        $this->server = $server;
        $this->processEvent = $processEvent;
    }

    /**
     * @http\resolution({method: "POST", path: "/aloleiro/provider/sinch/process-event"})
     */
    public function process()
    {
        /** @var array $post */
        $post = $this->server->resolveBody();

        $response = $this->processEvent->process($post);

        $this->server->sendResponse($response);
    }
}
