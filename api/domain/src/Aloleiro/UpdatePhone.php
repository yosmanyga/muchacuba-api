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
    private $pickProfile;

    /**
     * @var ManagePhoneStorage
     */
    private $managePhoneStorage;

    /**
     * @param PickProfile $pickProfile
     * @param ManagePhoneStorage  $managePhoneStorage
     */
    public function __construct(
        PickProfile $pickProfile,
        ManagePhoneStorage $managePhoneStorage
    )
    {
        $this->pickProfile = $pickProfile;
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

        if ($result->getMatchedCount() === 0) {
            throw new NonExistentPhoneException();
        }
    }
}