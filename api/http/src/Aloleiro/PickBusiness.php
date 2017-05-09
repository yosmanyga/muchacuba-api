<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\PickProfile as DomainPickProfile;
use Muchacuba\Aloleiro\PickBusiness as DomainPickBusiness;
use Muchacuba\Aloleiro\CollectPhones as DomainCollectPhones;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class PickBusiness
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPickBusiness
     */
    private $pickBusiness;

    /**
     * @var DomainPickProfile
     */
    private $pickProfile;

    /**
     * @var DomainCollectPhones
     */
    private $collectPhones;
    
    /**
     * @param Server              $server
     * @param DomainPickBusiness  $pickBusiness
     * @param DomainPickProfile   $pickProfile
     * @param DomainCollectPhones $collectPhones
     */
    public function __construct(
        Server $server,
        DomainPickBusiness $pickBusiness,
        DomainPickProfile $pickProfile,
        DomainCollectPhones $collectPhones
    ) {
        $this->server = $server;
        $this->pickBusiness = $pickBusiness;
        $this->pickProfile = $pickProfile;
        $this->collectPhones = $collectPhones;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "GET", path: "/aloleiro/pick-business"})
     *
     * @param string $uniqueness
     */
    public function pick($uniqueness)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        $business = $this->pickBusiness->pick($profile->getBusiness());

        $this->server->sendResponse($business);
    }
}
