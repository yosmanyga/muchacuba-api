<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business\InvalidDataException;
use Muchacuba\Aloleiro\UpdateBusiness as DomainUpdateBusiness;
use Muchacuba\Aloleiro\CollectPhones as DomainCollectPhones;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class UpdateBusiness
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainUpdateBusiness
     */
    private $updateBusiness;

    /**
     * @var DomainCollectPhones
     */
    private $collectPhones;
    
    /**
     * @param Server               $server
     * @param DomainUpdateBusiness $updateBusiness
     * @param DomainCollectPhones  $collectPhones
     */
    public function __construct(
        Server $server,
        DomainUpdateBusiness $updateBusiness,
        DomainCollectPhones $collectPhones
    ) {
        $this->server = $server;
        $this->updateBusiness = $updateBusiness;
        $this->collectPhones = $collectPhones;
    }

    /**
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "POST", uri: "/aloleiro/update-business"})
     *
     * @param string $uniqueness
     */
    public function update($uniqueness)
    {
        $post = $this->server->resolveBody();

        try {
            $this->updateBusiness->update(
                $uniqueness,
                $post['profitPercent']
            );
        } catch (InvalidDataException $e) {
            $this->server->sendResponse([
                'field' => $e->getField()
            ], 422);

            return;
        }

        $this->server->sendResponse();
    }
}
