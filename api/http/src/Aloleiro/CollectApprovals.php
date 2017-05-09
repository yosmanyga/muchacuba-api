<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CollectApprovals as DomainCollectApprovals;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectApprovals
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectApprovals
     */
    private $collectApprovals;

    /**
     * @param Server              $server
     * @param DomainCollectApprovals $collectApprovals
     */
    public function __construct(
        Server $server,
        DomainCollectApprovals $collectApprovals
    ) {
        $this->server = $server;
        $this->collectApprovals = $collectApprovals;
    }

    /**
     * @http\authorization({roles: ["aloleiro_admin"]})
     * @http\resolution({method: "GET", path: "/aloleiro/collect-approvals"})
     */
    public function collect()
    {
        $approvals = $this->collectApprovals->collect();

        $this->server->sendResponse($approvals);
    }
}
