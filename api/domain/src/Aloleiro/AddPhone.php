<?php

namespace Muchacuba\Aloleiro;

use MongoDB\Driver\Exception\BulkWriteException;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddPhone
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
     * @throws ExistentPhoneException
     */
    public function add($uniqueness, $number, $name)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        try {
            $this->managePhoneStorage->connect()->insertOne(new Phone(
                $number,
                $profile->getBusiness(),
                $name
            ));
        } catch (BulkWriteException $e) {
            if ($e->getWriteResult()->getWriteErrors()[0]->getCode() == 11000) {
                throw new ExistentPhoneException();
            }

            throw $e;
        }
    }
}