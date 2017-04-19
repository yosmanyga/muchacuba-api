<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Rate\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdateRate
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage  $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $countryName
     * @param string $countryCurrencyExchange
     */
    public function update($countryName, $countryCurrencyExchange)
    {
        $this->manageStorage->connect()->updateOne(
            [
                'countryName' => $countryName
            ],
            ['$set' => ['countryCurrencyExchange' => $countryCurrencyExchange]]
        );
    }
}