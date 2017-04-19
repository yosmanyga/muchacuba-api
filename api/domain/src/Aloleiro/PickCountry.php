<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Country\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickCountry
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
    ) {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $name
     *
     * @return Country
     */
    public function pick($name)
    {
        /** @var Country $country */
        $country = $this->manageStorage->connect()
            ->findOne([
                'name' => $name,
            ]);

        return $country;
    }
}
