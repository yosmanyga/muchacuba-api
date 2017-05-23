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
     * @param string $country
     * @param string $network
     * @param string $price
     */
    public function add(
        $country,
        $network,
        $price
    )
    {
        $this->manageStorage->connect()->insertOne(new Rate(
            uniqid(),
            $country,
            $network,
            $price
        ));
    }
}