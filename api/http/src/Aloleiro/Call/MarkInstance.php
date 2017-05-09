<?php

namespace Muchacuba\Http\Aloleiro\Call;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\Call\MarkInstance as DomainMarkInstance;
use Symsonte\Http\Server;
use Muchacuba\Aloleiro\CollectDailyClientCalls as DomainCollectDailyClientCalls;
/**
 * @di\controller({deductible: true})
 */
class MarkInstance
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainMarkInstance
     */
    private $markInstance;

    /**
     * @var DomainCollectDailyClientCalls
     */
    private $collectDailyClientCalls;

    /**
     * @param Server                        $server
     * @param DomainMarkInstance            $markInstance
     * @param DomainCollectDailyClientCalls $collectDailyClientCalls
     */
    public function __construct(
        Server $server,
        DomainMarkInstance $markInstance,
        DomainCollectDailyClientCalls $collectDailyClientCalls
    ) {
        $this->server = $server;
        $this->markInstance = $markInstance;
        $this->collectDailyClientCalls = $collectDailyClientCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "POST", path: "/aloleiro/call/mark-instance-as-did-speak"})
     *
     * @param Business $business
     */
    public function markAsDidSpeak(Business $business)
    {
        $post = $this->server->resolveBody();

        $this->markInstance->markAsDidSpeak(
            $business,
            $post['call'],
            $post['id']
        );

        $calls = $this->collectDailyClientCalls->collect($business);

        $this->server->sendResponse($calls);
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "POST", path: "/aloleiro/call/mark-instance-as-did-not-speak"})
     *
     * @param Business $business
     */
    public function markAsDidNotSpeak(Business $business)
    {
        $post = $this->server->resolveBody();

        $this->markInstance->markAsDidNotSpeak(
            $business,
            $post['call'],
            $post['id']
        );

        $calls = $this->collectDailyClientCalls->collect($business);

        $this->server->sendResponse($calls);
    }
}
