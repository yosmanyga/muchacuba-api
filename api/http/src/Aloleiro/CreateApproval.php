<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\CreateApproval as DomainCreateApproval;
use Muchacuba\Aloleiro\CollectApprovals as DomainCollectApprovals;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CreateApproval
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCreateApproval
     */
    private $createApproval;

    /**
     * @var DomainCollectApprovals
     */
    private $collectApprovals;
    
    /**
     * @param Server                 $server
     * @param DomainCreateApproval   $createApproval
     * @param DomainCollectApprovals $collectApprovals
     */
    public function __construct(
        Server $server,
        DomainCreateApproval $createApproval,
        DomainCollectApprovals $collectApprovals
    ) {
        $this->server = $server;
        $this->createApproval = $createApproval;
        $this->collectApprovals = $collectApprovals;
    }

    /**
     * @http\authorization({roles: ["aloleiro_admin"]})
     * @http\resolution({method: "POST", path: "/aloleiro/add-approval"})
     */
    public function add()
    {
        $post = $this->server->resolveBody();

        $roles = [];
        foreach ($post['roles'] as $role => $enabled) {
            if ($enabled) {
                $roles[] = $role;
            }
        }

        $this->createApproval->create(
            $post['email'],
            $post['business'],
            $roles
        );

        $this->server->sendResponse($this->collectApprovals->collect());
    }
}
