<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\ProcessEvent as DomainProcessEvent;
use Muchacuba\Aloleiro\CollectPhones as DomainCollectPhones;
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
     * @var DomainCollectPhones
     */
    private $collectPhones;
    
    /**
     * @param Server              $server
     * @param DomainProcessEvent  $processEvent
     * @param DomainCollectPhones $collectPhones
     */
    public function __construct(
        Server $server,
        DomainProcessEvent $processEvent,
        DomainCollectPhones $collectPhones
    ) {
        $this->server = $server;
        $this->processEvent = $processEvent;
        $this->collectPhones = $collectPhones;
    }

    /**
     * @http\resolution({method: "POST", uri: "/aloleiro/process-event"})
     */
    public function process()
    {
        /** @var array $post */
        $post = $this->server->resolveBody();

        $result = $this->processEvent->process($post);

        $this->server->sendResponse($result);
    }
}
