<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;
use MongoDB\UpdateResult;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdatePhone
{
    /**
     * @var PickProfile
     */
    private $PickProfile;

    /**
     * @var ManagePhoneStorage
     */
    private $managePhoneStorage;

    /**
     * @param PickProfile $PickProfile
     * @param ManagePhoneStorage  $managePhoneStorage
     */
    public function __construct(
        PickProfile $PickProfile,
        ManagePhoneStorage $managePhoneStorage
    )
    {
        $this->pickProfile = $PickProfile;
        $this->managePhoneStorage = $managePhoneStorage;
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
        $profile = $this->pickProfile->pick($uniqueness);

        /** @var UpdateResult $result */
        $result = $this->managePhoneStorage->connect()->updateOne(
            [
                '_id' => $number,
                'business' => $profile->getBusiness()
            ],
            ['$set' => ['name' => $name]]
        );

        if ($result->getModifiedCount() === 0) {
            throw new NonExistentPhoneException();
        }
    }
}