<?php

namespace Muchacuba\Http\Mule\Me;

use Muchacuba\Mule\NonExistentOfferException;
use Muchacuba\Mule\Me\PickOffer as DomainPickOffer;
use Muchacuba\Mule\NonExistentProfileException;
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
     * @http\resolution({method: "GET", path: "/mule/me/pick-offer"})
     * @http\authorization({roles: ["mule_user"]})
     *
     * @param string $uniqueness
     */
    public function pick($uniqueness)
    {
        try {
            $profile = $this->pickOffer->pick($uniqueness);
        } catch (NonExistentProfileException $e) {
            $this->server->sendResponse(null, 404);

            return;
        } catch (NonExistentOfferException $e) {
            $this->server->sendResponse(null, 404);

            return;
        }

        $this->server->sendResponse($profile);
    }
}
