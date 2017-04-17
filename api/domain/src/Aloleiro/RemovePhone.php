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
     *
     * @throws NonExistentPhoneException
     */
    public function remove($uniqueness, $number)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        /** @var DeleteResult $result */
        $result = $this->managePhoneStorage->connect()->deleteOne([
            '_id' => $number,
            'business' => $profile->getBusiness()
        ]);

        if ($result->getDeletedCount() === 0) {
            throw new NonExistentPhoneException();
        }
    }
}