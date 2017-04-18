<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\AddPhone as DomainAddPhone;
use Muchacuba\Aloleiro\CollectPhones as DomainCollectPhones;
use Muchacuba\Aloleiro\ExistentPhoneException;
use Muchacuba\Aloleiro\Phone\InvalidDataException;
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
     * @http\authorization({roles: ["aloleiro_owner"]})
     * @http\resolution({method: "POST", uri: "/aloleiro/add-phone"})
     *
     * @param string $uniqueness
     */
    public function search($uniqueness)
    {
        $post = $this->server->resolveBody();

        try {
            $this->addPhone->add(
                $uniqueness,
                $post['number'],
                $post['name']
            );
        } catch (InvalidDataException $e) {
            $this->server->sendResponse([
                'field' => $e->getField(),
                'type' => 'invalid'
            ], 422);

            return;
        } catch (ExistentPhoneException $e) {
            $this->server->sendResponse([
                'field' => 'number',
                'type' => 'duplicated'
            ], 422);

            return;
        }

        $this->server->sendResponse($this->collectPhones->collect($uniqueness));
    }
}
