<?php

namespace Muchacuba\Http\Mule;

use Muchacuba\Mule\NonExistentProfileException;
use Muchacuba\Mule\PickOffer as DomainPickOffer;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class PickOffer
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPickOffer
     */
    private $pickOffer;

    /**
     * @param Server          $server
     * @param DomainPickOffer $pickOffer
     */
    public function __construct(
        Server $server,
        DomainPickOffer $pickOffer
    ) {
        $this->server = $server;
        $this->pickOffer = $pickOffer;
    }

    /**
     * @http\resolution({method: "GET", path: "/mule/pick-offer/{id}"})
     *
     * @param string $id
     */
    public function pick($id)
    {
        $profile = $this->pickOffer->pick($id);

        $this->server->sendResponse($profile);
    }
}
