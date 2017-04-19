<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Country\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddCountry
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
     * @param string $name
     * @param string $translation
     * @param float  $currencyExchange
     */
    public function add($name, $translation, $currencyExchange)
    {
        $this->manageStorage->connect()->insertOne(new Country(
            uniqid(),
            $name,
            $translation,
            $currencyExchange
        ));
    }
}