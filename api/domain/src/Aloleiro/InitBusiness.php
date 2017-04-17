<?php

namespace Muchacuba\Aloleiro;

use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Profile\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class InitBusiness
{
    /**
     * @var CreateBusiness
     */
    private $createBusiness;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param CreateBusiness $createBusiness
     * @param ManageStorage  $manageStorage
     */
    public function __construct(
        CreateBusiness $createBusiness,
        ManageStorage $manageStorage
    )
    {
        $this->createBusiness = $createBusiness;
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param int    $profitFactor
     * @param float  $balance
     * @param string $uniqueness
     *
     * @throws NonExistentProfileException
     */
    public function init($profitFactor, $balance, $uniqueness)
    {
        $business = $this->createBusiness->create($profitFactor, $balance);

        /** @var UpdateResult $result */
        $result = $this->manageStorage->connect()->updateOne(
            ['_id' => $uniqueness],
            ['$set' => ['business' => $business]]
        );

        if ($result->getModifiedCount() == 0) {
            throw new NonExistentProfileException();
        }
    }
}