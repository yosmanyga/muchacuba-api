<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\PrepareClientRates as DomainPrepareClientRates;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class DownloadClientRates
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPrepareClientRates
     */
    private $prepareRates;

    /**
     * @param Server                   $server
     * @param DomainPrepareClientRates $prepareRates
     */
    public function __construct(
        Server $server,
        DomainPrepareClientRates $prepareRates
    ) {
        $this->server = $server;
        $this->prepareRates = $prepareRates;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "GET", uri: "/aloleiro/download-client-rates"})
     *
     * @param string $uniqueness
     */
    public function download($uniqueness)
    {
        $file = $this->prepareRates->prepare($uniqueness);

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
