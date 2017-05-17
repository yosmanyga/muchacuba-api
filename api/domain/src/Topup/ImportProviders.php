<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Provider\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportProviders
{
    /**
     * @var QueryApi
     */
    private $queryApi;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param QueryApi      $queryApi
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        QueryApi $queryApi,
        ManageStorage $manageStorage
    )
    {
        $this->queryApi = $queryApi;
        $this->manageStorage = $manageStorage;
    }

    public function import()
    {
        $providers = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetProviders'
        );

        foreach ($providers['Items'] as $provider) {
            $this->manageStorage->connect()->insertOne(new Provider(
                $provider['ProviderCode'],
                $provider
            ));
        }
    }
}
