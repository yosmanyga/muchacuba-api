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
        $countries = $this->manageStorage->connect()->find();

        return iterator_to_array($countries);
    }
}
