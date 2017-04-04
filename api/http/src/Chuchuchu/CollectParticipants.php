<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\CollectParticipants as DomainCollectParticipants;
use Muchacuba\Chuchuchu\UnauthorizedException;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectParticipants
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectParticipants
     */
    private $collectParticipants;

    /**
     * @param Server                    $server
     * @param DomainCollectParticipants $collectParticipants
     */
    public function __construct(
        Server $server,
        DomainCollectParticipants $collectParticipants
    ) {
        $this->server = $server;
        $this->collectParticipants = $collectParticipants;
    }

    /**
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "GET", uri: "/chuchuchu/collect-participants/{conversation}"})
     *
     * @param string $uniqueness
     * @param string $conversation
     */
    public function collectParticipants($uniqueness, $conversation)
    {
        try {
            $participants = $this->collectParticipants->collect(
                $uniqueness,
                $conversation
            );
        } catch (UnauthorizedException $e) {
            $this->server->sendResponse(null, 401);

            return;
        }

        $this->server->sendResponse($participants);
    }
}
