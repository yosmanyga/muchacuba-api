<?php

namespace Muchacuba\Aloleiro;

use MongoDB\DeleteResult;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;
use Muchacuba\Aloleiro\Profile\ManageStorage as ManageProfileStorage;
use MongoDB\UpdateResult;

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
     * @var ManageProfileStorage
     */
    private $manageProfileStorage;

    /**
     * @param ManagePhoneStorage   $managePhoneStorage
     * @param ManageProfileStorage $manageProfileStorage
     */
    public function __construct(
        ManagePhoneStorage $managePhoneStorage,
        ManageProfileStorage $manageProfileStorage
    )
    {
        $this->managePhoneStorage = $managePhoneStorage;
        $this->manageProfileStorage = $manageProfileStorage;
    }

    /**
     * @param string $uniqueness
     * @param string $number
     *
     * @throws NonExistentPhoneException
     */
    public function remove($uniqueness, $number)
    {
        /** @var UpdateResult $result */
        $result = $this->manageProfileStorage->connect()->updateOne(
            ['_id' => $uniqueness],
            ['$pull' => ['phones' => $number]]
        );

        if ($result->getModifiedCount() === 0) {
            throw new NonExistentPhoneException();
        }

        /** @var DeleteResult $result */
        $result = $this->managePhoneStorage->connect()->deleteOne([
            '_id' => $number
        ]);

        if ($result->getDeletedCount() === 0) {
            // TODO: Corrupted db?
        }
    }
}