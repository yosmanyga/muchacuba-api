<?php

namespace Muchacuba\Http\Aloleiro;

use Cubalider\Voip\CollectLogs as DomainCollectLogs;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectLogs
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectLogs
     */
    private $collectLogs;

    /**
     * @param Server            $server
     * @param DomainCollectLogs $collectLogs
     */
    public function __construct(
        Server $server,
        DomainCollectLogs $collectLogs
    ) {
        $this->server = $server;
        $this->collectLogs = $collectLogs;
    }

    /**
     * @http\authorization({roles: ["aloleiro_admin"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-logs"})
     */
    public function collect()
    {
        $logs = $this->collectLogs->collect();

        $this->server->sendResponse($logs);
    }
}
