<?php

namespace Muchacuba\Cli\Aloleiro\Maintenance;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\Maintenance\UpgradeStorage as DomainUpgradeStorage;

/**
 * @di\command({deductible: true})
 */
class UpgradeStorage
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainUpgradeStorage
     */
    private $upgradeStorage;

    /**
     * @param Server               $server
     * @param DomainUpgradeStorage $upgradeStorage
     */
    public function __construct(
        Server $server,
        DomainUpgradeStorage $upgradeStorage
    )
    {
        $this->server = $server;
        $this->upgradeStorage = $upgradeStorage;
    }

    /**
     * @cli\resolution({command: "aloleiro.maintenance.upgrade-storage"})
     */
    public function promote()
    {
        $ids = $this->upgradeStorage->upgrade();

        if (!empty($ids)) {
            $this->server->resolveOutput()->outln(implode('\r', $ids));
        }
    }
}
