<?php

namespace Muchacuba\Aloleiro;

use MongoDB\DeleteResult;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;
use Muchacuba\Aloleiro\Profile\ManageStorage as ManageProfileStorage;
use MongoDB\Operation\Update;
use MongoDB\UpdateResult;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdatePhone
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
     * @param string $name
     *
     * @throws NonExistentPhoneException
     */
    public function update($uniqueness, $number, $name)
    {
        $profile = $this->manageProfileStorage->connect()->findOne([
            '_id' => $uniqueness,
            'phones' => ['$in' => [$number]]
        ]);

        if (is_null($profile)) {
            throw new NonExistentPhoneException();
        }

        /** @var UpdateResult $result */
        $result = $this->managePhoneStorage->connect()->updateOne(
            ['_id' => $number],
            ['$set' => ['name' => $name]]
        );

        if ($result->getModifiedCount() === 0) {
            // TODO: Corrupted db?
        }
    }
}