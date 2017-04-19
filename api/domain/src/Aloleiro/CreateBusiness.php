<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Business\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class CreateBusiness
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
     * @param string|null $id
     *
     * @return string
     */
    public function create(
        $balance = 0.0,
        $profitPercent,
        $id = null)
    {
        $id = $id ?: uniqid();

        $this->manageStorage->connect()->insertOne(new Business(
            $id,
            $balance,
            $profitPercent
        ));

        return $id;
    }
}