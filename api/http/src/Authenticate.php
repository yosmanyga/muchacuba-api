<?php

namespace Muchacuba\Http;

use Muchacuba\InvalidTokenException;
use Muchacuba\Authenticate as DomainAuthenticate;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class Authenticate
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainAuthenticate
     */
    private $authenticate;

    /**
     * @param Server             $server
     * @param DomainAuthenticate $authenticate
     */
    public function __construct(Server $server, DomainAuthenticate $authenticate)
    {
        $this->server = $server;
        $this->authenticate = $authenticate;
    }

    /**
     * @param string $token
     *
     * @http\resolution({method: "GET", uri: "/authenticate/{token}"})
     */
    public function authenticate($token)
    {
        try {
            $token = $this->authenticate->authenticate($token);
        } catch (InvalidTokenException $e) {
            $this->server->sendResponse("Invalid token", 500);
        }

        $this->server->sendResponse(['token' => $token]);
    }
}
