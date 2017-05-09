<?php

namespace Muchacuba\Http\Mule\Me;

use Muchacuba\Mule\Me\ManageOffer as DomainManageOffer;
use Muchacuba\Mule\Offer\InvalidDataException;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ManageOffer
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainManageOffer
     */
    private $manageOffer;

    /**
     * @param Server            $server
     * @param DomainManageOffer $manageOffer
     */
    public function __construct(
        Server $server,
        DomainManageOffer $manageOffer
    ) {
        $this->server = $server;
        $this->manageOffer = $manageOffer;
    }

    /**
     * @param string $uniqueness
     *
     * @http\resolution({method: "POST", path: "/mule/me/create-offer"})
     * @http\authorization({roles: ["mule_user"]})
     */
    public function create($uniqueness)
    {
        $post = $this->server->resolveBody();

        try {
            $this->manageOffer->create(
                $uniqueness,
                null,
                $post['name'],
                $post['contact'],
                $post['address'],
                $post['coordinates'],
                $post['destinations'],
                $post['description'],
                $post['trips']
            );
        } catch (InvalidDataException $e) {
            $this->server->sendResponse(
                [
                    'error' => [
                        'field' => $e->getField(),
                        'type' => $e->getType()
                    ]
                ]
            );

            return;
        }

        $this->server->sendResponse();
    }

    /**
     * @param string $uniqueness
     *
     * @http\resolution({method: "POST", path: "/mule/me/update-offer"})
     * @http\authorization({roles: ["mule_user"]})
     */
    public function update($uniqueness)
    {
        $post = $this->server->resolveBody();

        try {
            $this->manageOffer->update(
                $uniqueness,
                $post['id'],
                $post['name'],
                $post['contact'],
                $post['address'],
                $post['coordinates'],
                $post['destinations'],
                $post['description'],
                $post['trips']
            );
        } catch (InvalidDataException $e) {
            $this->server->sendResponse(
                [
                    'error' => [
                        'field' => $e->getField(),
                        'type' => $e->getType()
                    ]
                ]
            );

            return;
        }

        $this->server->sendResponse();
    }
}
