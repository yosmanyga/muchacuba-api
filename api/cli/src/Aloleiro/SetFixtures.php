<?php

namespace Muchacuba\Cli\Aloleiro;

use Symsonte\Cli\Server;
use Muchacuba\Aloleiro\SetFixtures as DomainSetFixtures;

/**
 * @di\command({deductible: true})
 */
class SetFixtures
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainSetFixtures
     */
    private $setFixtures;

    /**
     * @param Server            $server
     * @param DomainSetFixtures $setFixtures
     */
    public function __construct(
        Server $server,
        DomainSetFixtures $setFixtures
    )
    {
        $this->server = $server;
        $this->setFixtures = $setFixtures;
    }

    /**
     * @cli\resolution({command: "aloleiro.set-fixtures"})
     */
    public function promote()
    {
        $input = $this->server->resolveInput();

        $this->setFixtures->set(
            $input->get('2'),
            $input->get('3'),
            $input->get('4'),
            $input->get('5')
        );
    }
}
