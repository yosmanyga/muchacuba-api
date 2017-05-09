<?php

namespace Muchacuba\Aloleiro;

use MongoDB\DeleteResult;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CancelCall
{
    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(
        ManageCallStorage $manageCallStorage
    )
    {
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * @param Business $business
     * @param string $id
     *
     * @throws NonExistentCallException
     */
    public function cancel(Business $business, $id)
    {
        /** @var DeleteResult $result */
        $result = $this->manageCallStorage->connect()->deleteOne([
            '_id' => $id,
            'business' => $business->getId(),
            'instances' => []
        ]);

        if ($result->getDeletedCount() === 0) {
            throw new NonExistentCallException();
        }
    }
}