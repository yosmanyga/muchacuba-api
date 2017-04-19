<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Rate\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddRate
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

    public function add(
        $countryName,
        $countryTranslation,
        $countryCurrencyExchange,
        $type,
        $code,
        $value
    )
    {
        $this->manageStorage->connect()->insertOne(new Rate(
            uniqid(),
            $countryName,
            $countryTranslation,
            $countryCurrencyExchange,
            $type,
            $code,
            $value
        ));
    }
}