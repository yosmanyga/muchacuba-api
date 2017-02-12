<?php

namespace Muchacuba\Http\Internauta;

use Symsonte\Http\Server;
use Muchacuba\Internauta\CollectLogs as DomainCollectLogs;

/**
 * @di\controller({deductible: true})
 */
class CollectLogs
{
    /**
     * @var DomainCollectLogs
     */
    private $collectLogs;

    /**
     * @var Server
     */
    private $server;

    /**
     * @param DomainCollectLogs $collectLogs
     * @param Server              $server
     */
    public function __construct(
        DomainCollectLogs $collectLogs,
        Server $server
    )
    {
        $this->collectLogs = $collectLogs;
        $this->server = $server;
    }

    /**
     * @http\resolution({method: "GET", uri: "/internauta/collect-logs"})
     */
    public function collect()
    {
        $logs = $this->collectLogs->collect();

        $this->server->sendResponse($logs);
    }
}
