<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Country\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportCountries
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
        $countries = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetCountries'
        );

        foreach ($countries['Items'] as $country) {
            $this->manageStorage->connect()->insertOne(new Country(
                $country['CountryIso'],
                $country['CountryName'],
                $country['InternationalDialingInformation']['Prefix'],
                $country['InternationalDialingInformation']['MinimumLength'],
                $country['InternationalDialingInformation']['MaximumLength'],
                $country
            ));
        }
    }
}
