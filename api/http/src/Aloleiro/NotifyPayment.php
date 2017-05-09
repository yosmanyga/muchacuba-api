<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\NotifyPayment as DomainNotifyPayment;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class NotifyPayment
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainNotifyPayment
     */
    private $notifyPayment;

    /**
     * @param Server              $server
     * @param DomainNotifyPayment $notifyPayment
     */
    public function __construct(
        Server $server,
        DomainNotifyPayment $notifyPayment
    ) {
        $this->server = $server;
        $this->notifyPayment = $notifyPayment;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "POST", path: "/aloleiro/notify-payment"})
     *
     * @param string $uniqueness
     */
    public function notify($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->notifyPayment->notify(
            $uniqueness,
            $post['reference']
        );

        $this->server->sendResponse();
    }
}
