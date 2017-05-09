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

    /**
     * @param string $countryName
     * @param string $countryTranslation
     * @param string $network
     * @param string $prefix
     * @param string $price
     */
    public function add(
        $countryName,
        $countryTranslation,
        $network,
        $prefix,
        $price
    )
    {
        $this->manageStorage->connect()->insertOne(new Rate(
            uniqid(),
            $countryName,
            $countryTranslation,
            $network,
            $prefix,
            $price
        ));
    }
}