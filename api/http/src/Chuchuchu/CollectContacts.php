<?php

namespace Muchacuba\Http\Chuchuchu;

use Muchacuba\Chuchuchu\CollectContacts as DomainCollectContacts;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class CollectContacts
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainCollectContacts
     */
    private $collectContacts;

    /**
     * @param Server                $server
     * @param DomainCollectContacts $collectContacts
     */
    public function __construct(
        Server $server,
        DomainCollectContacts $collectContacts
    ) {
        $this->server = $server;
        $this->collectContacts = $collectContacts;
    }

    /**
     * @http\authorization({roles: ["user"]})
     * @http\resolution({method: "GET", uri: "/chuchuchu/collect-contacts"})
     *
     * @param string $uniqueness
     */
    public function collect($uniqueness)
    {
        $contacts = $this->collectContacts->collect(
            $uniqueness
        );

        $this->server->sendResponse($contacts);
    }
}
