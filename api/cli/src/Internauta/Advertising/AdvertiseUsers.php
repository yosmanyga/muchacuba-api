<?php

namespace Muchacuba\Cli\Internauta\Advertising;

use Symsonte\Cli\Server;
use Muchacuba\Internauta\Advertising\AdvertiseUsers as DomainAdvertiseUsers;

/**
 * @di\command({deductible: true})
 */
class AdvertiseUsers
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainAdvertiseUsers
     */
    private $advertiseUsers;

    /**
     * @param Server               $server
     * @param DomainAdvertiseUsers $advertiseUsers
     */
    public function __construct(
        Server $server,
        DomainAdvertiseUsers $advertiseUsers
    )
    {
        $this->server = $server;
        $this->advertiseUsers = $advertiseUsers;
    }

    /**
     * @cli\resolution({command: "internauta.advertising.advertise-users"})
     */
    public function process()
    {
        $this->server->resolveOutput()->outln(
            $this->advertiseUsers->advertise(100)
        );
    }
}
