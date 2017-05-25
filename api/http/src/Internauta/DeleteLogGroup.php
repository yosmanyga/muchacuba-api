<?php

namespace Muchacuba\Http\Internauta;

use Symsonte\Http\Server;
use Muchacuba\Internauta\DeleteLogGroup as DomainDeleteLogGroup;

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
     * @var Server
     */
    private $server;

    /**
     * @param DomainDeleteLogGroup $deleteLogGroup
     * @param Server               $server
     */
    public function __construct(
        DomainDeleteLogGroup $deleteLogGroup,
        Server $server
    )
    {
        $this->deleteLogGroup = $deleteLogGroup;
        $this->server = $server;
    }

    /**
     * @http\resolution({method: "GET", path: "/internauta/delete-log-group/{id}"})
     *
     * @param string $id
     */
    public function delete($id)
    {
        $this->deleteLogGroup->delete($id);

        $this->server->sendResponse();
    }
}
