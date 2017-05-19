<?php

namespace Muchacuba\Cli\Aloleiro\Voip;

use Symsonte\Cli\Server;
use Cubalider\Voip\Nexmo\PostProcessCalls as DomainPostProcessCalls;

/**
 * @di\command({deductible: true})
 */
class PostProcessCalls
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPostProcessCalls
     */
    private $postProcessCalls;

    /**
     * @param Server                 $server
     * @param DomainPostProcessCalls $postProcessCalls
     */
    public function __construct(
        Server $server,
        DomainPostProcessCalls $postProcessCalls
    )
    {
        $this->server = $server;
        $this->postProcessCalls = $postProcessCalls;
    }

    /**
     * @cli\resolution({command: "aloleiro.voip.post_process_calls"})
     */
    public function promote()
    {
        $this->postProcessCalls->postProcess();
    }
}
