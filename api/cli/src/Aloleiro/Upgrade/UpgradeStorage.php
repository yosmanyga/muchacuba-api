<?php

namespace Muchacuba\Cli\Aloleiro\Upgrade;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\Upgrade\UpgradeStorage as DomainUpgradeStorage;

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
     * @cli\resolution({command: "aloleiro.upgrade.upgrade-storage"})
     */
    public function promote()
    {
        $ids = $this->upgradeStorage->upgrade();

        $this->server->resolveOutput()->outln(implode('\r', $ids));
    }
}
