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