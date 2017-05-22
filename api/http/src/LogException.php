<?php

namespace Muchacuba\Http;

use Muchacuba\LogException as DomainLogException;
use Symsonte\Http\Server;

/**
 * @di\service({deductible: true})
 */
class LogException
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainLogException
     */
    private $logException;

    /**
     * @param Server             $server
     * @param DomainLogException $logException
     */
    public function __construct(
        Server $server,
        DomainLogException $logException
    ) {
        $this->server = $server;
        $this->logException = $logException;
    }

    /**
     * @param \Exception|\Throwable $e
     */
    public function __invoke($e)
    {
        $this->logException->log($e);

        $this->server->sendResponse(null, 500);
    }
}
