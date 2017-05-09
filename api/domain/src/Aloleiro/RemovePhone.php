<?php

namespace Muchacuba\Aloleiro;

use MongoDB\DeleteResult;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class RemovePhone
{
    /**
     * @var ManagePhoneStorage
     */
    private $managePhoneStorage;

    /**
     * @param ManagePhoneStorage  $managePhoneStorage
     */
    public function __construct(
        ManagePhoneStorage $managePhoneStorage
    )
    {
        $this->managePhoneStorage = $managePhoneStorage;
    }

    /**
     * @param Business $business
     * @param string   $number
     *
     * @throws NonExistentPhoneException
     */
    public function remove(Business $business, $number)
    {
        /** @var DeleteResult $result */
        $result = $this->managePhoneStorage->connect()->deleteOne([
            '_id' => $number,
            'business' => $business->getId()
        ]);

        if ($result->getDeletedCount() === 0) {
            throw new NonExistentPhoneException();
        }
    }
}