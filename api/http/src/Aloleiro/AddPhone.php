<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\AddPhone as DomainAddPhone;
use Muchacuba\Aloleiro\CollectPhones as DomainCollectPhones;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class AddPhone
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainAddPhone
     */
    private $addPhone;

    /**
     * @var DomainCollectPhones
     */
    private $collectPhones;
    
    /**
     * @param Server              $server
     * @param DomainAddPhone      $addPhone
     * @param DomainCollectPhones $collectPhones
     */
    public function __construct(
        Server $server,
        DomainAddPhone $addPhone,
        DomainCollectPhones $collectPhones
    ) {
        $this->server = $server;
        $this->addPhone = $addPhone;
        $this->collectPhones = $collectPhones;
    }

    /**
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "POST", uri: "/aloleiro/add-phone"})
     *
     * @param string $uniqueness
     */
    public function search($uniqueness)
    {
        $post = $this->server->resolveBody();

        $this->addPhone->add(
            $uniqueness,
            $post['number'], 
            $post['name']
        );

        $this->server->sendResponse($this->collectPhones->collect($uniqueness));
    }
}
