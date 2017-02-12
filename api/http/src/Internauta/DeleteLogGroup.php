<?php

namespace Muchacuba\Http\Internauta;

use Symsonte\Http\Server;
use Muchacuba\Internauta\DeleteLogGroup as DomainDeleteLogGroup;
use Muchacuba\Internauta\CollectLogs as DomainCollectLogs;

/**
 * @di\controller({deductible: true})
 */
class DeleteLogGroup
{
    /**
     * @var DomainDeleteLogGroup
     */
    private $deleteLogGroup;

    /**
     * @var DomainCollectLogs
     */
    private $domainCollectLogs;

    /**
     * @var Server
     */
    private $server;

    /**
     * @param DomainDeleteLogGroup $deleteLogGroup
     * @param DomainCollectLogs    $domainCollectLogs
     * @param Server               $server
     */
    public function __construct(
        DomainDeleteLogGroup $deleteLogGroup,
        DomainCollectLogs $domainCollectLogs,
        Server $server
    )
    {
        $this->deleteLogGroup = $deleteLogGroup;
        $this->domainCollectLogs = $domainCollectLogs;
        $this->server = $server;
    }

    /**
     * @http\resolution({method: "GET", uri: "/internauta/delete-log-group/{id}"})
     *
     * @param string $id
     */
    public function delete($id)
    {
        $this->deleteLogGroup->delete($id);

        $this->server->sendResponse($this->domainCollectLogs->collect());
    }
}
