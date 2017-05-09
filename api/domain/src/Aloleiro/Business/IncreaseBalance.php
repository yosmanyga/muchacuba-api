<?php

namespace Muchacuba\Aloleiro\Business;

use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\NonExistentBusinessException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class IncreaseBalance
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
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param Business $business
     * @param string   $amount
     *
     * @throws NonExistentBusinessException
     */
    public function increase(Business $business, $amount)
    {
        /** @var UpdateResult $result */
        $result = $this->manageStorage->connect()->updateOne(
            ['_id' => $business->getId()],
            ['$inc' => ['balance' => (int) $amount]]
        );

        if ($result->getMatchedCount() == 0) {
            throw new NonExistentBusinessException();
        }
    }
}
