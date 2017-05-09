<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\PrepareSystemRates as DomainPrepareSystemRates;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class DownloadSystemRates
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPrepareSystemRates
     */
    private $prepareRates;

    /**
     * @param Server                   $server
     * @param DomainPrepareSystemRates $prepareRates
     */
    public function __construct(
        Server $server,
        DomainPrepareSystemRates $prepareRates
    ) {
        $this->server = $server;
        $this->prepareRates = $prepareRates;
    }

    /**
     * @http\resolution({method: "GET", path: "/aloleiro/download-system-rates"})
     */
    public function download()
    {
        $file = $this->prepareRates->prepare();

        $this->server->sendResponse(
            file_get_contents($file),
            null,
            [
                'Content-Disposition' => sprintf('attachment; filename=%s', basename($file)),
                'Content-Transfer-Encoding' => 'binary'
            ]
        );
    }
}
