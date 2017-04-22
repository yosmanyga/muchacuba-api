<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\AddBusiness as DomainAddBusiness;
use Muchacuba\Aloleiro\CollectBusinesses as DomainCollectBusinesses;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class AddBusiness
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainAddBusiness
     */
    private $addBusiness;

    /**
     * @var DomainCollectBusinesses
     */
    private $collectBusinesses;
    
    /**
     * @param Server                  $server
     * @param DomainAddBusiness       $addBusiness
     * @param DomainCollectBusinesses $collectBusinesses
     */
    public function __construct(
        Server $server,
        DomainAddBusiness $addBusiness,
        DomainCollectBusinesses $collectBusinesses
    ) {
        $this->server = $server;
        $this->addBusiness = $addBusiness;
        $this->collectBusinesses = $collectBusinesses;
    }

    /**
     * @http\authorization({roles: ["aloleiro_admin"]})
     * @http\resolution({method: "POST", uri: "/aloleiro/add-business"})
     */
    public function add()
    {
        $post = $this->server->resolveBody();

        $this->addBusiness->add(
            $post['balance'],
            $post['profitPercent'],
            $post['name'],
            $post['address']
        );

        $this->server->sendResponse($this->collectBusinesses->collect());
    }
}
