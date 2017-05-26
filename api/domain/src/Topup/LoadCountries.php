<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Country\Dialing;
use Muchacuba\Topup\Payload\ManageStorage as ManagePayloadStorage;
use Muchacuba\Topup\Country\ManageStorage as ManageCountryStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class LoadCountries
{
    /**
     * @var ManagePayloadStorage
     */
    private $managePayloadStorage;

    /**
     * @var ManageCountryStorage
     */
    private $manageCountryStorage;

    /**
     * @param ManagePayloadStorage $managePayloadStorage
     * @param ManageCountryStorage $manageCountryStorage
     */
    public function __construct(
        ManagePayloadStorage $managePayloadStorage,
        ManageCountryStorage $manageCountryStorage
    )
    {
        $this->managePayloadStorage = $managePayloadStorage;
        $this->manageCountryStorage = $manageCountryStorage;
    }

    public function load()
    {
        $this->manageCountryStorage->purge();

        /** @var Payload[] $countries */
        $countries = $this->managePayloadStorage->connect()->find([
            'type' => Payload::TYPE_COUNTRY
        ]);

        foreach ($countries as $country) {
            $dialings = [];
            foreach ($country->getData()['InternationalDialingInformation'] as $dialing) {
                $dialings[] = new Dialing(
                    $dialing['Prefix'],
                    $dialing['MinimumLength'],
                    $dialing['MaximumLength']
                );
            }

            $this->manageCountryStorage->connect()->insertOne(new Country(
                $country->getData()['CountryIso'],
                $country->getData()['CountryName'],
                $dialings
            ));
        }
    }
}
