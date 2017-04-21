<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Business\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddBusiness
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
     * @param float       $balance
     * @param int         $profitPercent
     * @param string      $name
     * @param string      $address
     * @param string|null $id
     *
     * @return string
     */
    public function add(
        $balance,
        $profitPercent,
        $name,
        $address,
        $id = null)
    {
        $id = $id ?: uniqid();

        $this->manageStorage->connect()->insertOne(new Business(
            $id,
            $balance,
            $profitPercent,
            $name,
            $address
        ));

        return $id;
    }
}