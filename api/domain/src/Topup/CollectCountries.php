<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Country\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectCountries
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @return Country[]
     */
    public function collect()
    {
        $countries = iterator_to_array($this->manageStorage->connect()->find());

        usort($countries, function(Country $countryA, Country $countryB) {
            return ($countryA->getName() < $countryB->getName()) ? -1 : 1;
        });

        return $countries;
    }
}
