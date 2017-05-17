<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Product\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddService
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
     * @param string      $country
     * @param string      $name
     * @param string      $logo
     * @param string|null $id
     *
     * @return string
     */
    public function add(
        $country,
        $name,
        $logo,
        $id = null)
    {
        $id = $id ?: uniqid();

        $this->manageStorage->connect()->insertOne(new Service(
            $id,
            $country,
            $name,
            $logo
        ));

        return $id;
    }
}