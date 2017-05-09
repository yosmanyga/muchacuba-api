<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
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
     * @http\resolution({method: "POST", path: "/aloleiro/download-client-rates"})
     *
     * @param Business $business
     */
    public function download(Business $business)
    {
        $file = $this->prepareRates->prepare($business);

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
