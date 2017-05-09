<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\RemovePhone as DomainRemovePhone;
use Muchacuba\Aloleiro\CollectPhones as DomainCollectPhones;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class RemovePhone
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainRemovePhone
     */
    private $removePhone;

    /**
     * @var DomainCollectPhones
     */
    private $collectPhones;
    
    /**
     * @param Server              $server
     * @param DomainRemovePhone   $removePhone
     * @param DomainCollectPhones $collectPhones
     */
    public function __construct(
        Server $server,
        DomainRemovePhone $removePhone,
        DomainCollectPhones $collectPhones
    ) {
        $this->server = $server;
        $this->removePhone = $removePhone;
        $this->collectPhones = $collectPhones;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "POST", path: "/aloleiro/remove-phone"})
     *
     * @param Business $business
     */
    public function remove(Business $business)
    {
        $post = $this->server->resolveBody();

        $this->removePhone->remove(
            $business,
            $post['number']
        );

        $this->server->sendResponse($this->collectPhones->collect($business));
    }
}
