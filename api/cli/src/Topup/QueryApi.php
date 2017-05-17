<?php

namespace Muchacuba\Cli\Topup;

use Muchacuba\Topup\QueryApi as DomainQueryApi;

/**
 * @di\command({deductible: true})
 */
class QueryApi
{
    /**
     * @var DomainQueryApi
     */
    private $queryApi;

    /**
     * @param DomainQueryApi $queryApi
     */
    public function __construct(DomainQueryApi $queryApi)
    {
        $this->queryApi = $queryApi;
    }

    /**
     * @cli\resolution({command: "topup.query_api"})
     */
    public function query()
    {
        $response = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetProductDescriptions',
            []
        );
    }
}
