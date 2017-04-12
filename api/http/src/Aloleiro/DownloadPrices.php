<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\PreparePrices as DomainPreparePrices;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class DownloadPrices
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPreparePrices
     */
    private $preparePrices;

    /**
     * @param Server              $server
     * @param DomainPreparePrices $preparePrices
     */
    public function __construct(
        Server $server,
        DomainPreparePrices $preparePrices
    ) {
        $this->server = $server;
        $this->preparePrices = $preparePrices;
    }

    /**
     * @http\resolution({method: "GET", uri: "/aloleiro/download-prices"})
     */
    public function download()
    {
        $file = $this->preparePrices->prepare();

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
