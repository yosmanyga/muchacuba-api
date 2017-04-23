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
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param PickProfile       $pickProfile
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(
        PickProfile $pickProfile,
        ManageCallStorage $manageCallStorage
    )
    {
        $this->pickProfile = $pickProfile;
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * @param string $uniqueness
     * @param string $id
     *
     * @throws NonExistentCallException
     */
    public function cancel($uniqueness, $id)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        /** @var DeleteResult $result */
        $result = $this->manageCallStorage->connect()->deleteOne([
            '_id' => $id,
            'business' => $profile->getBusiness(),
            'instances' => []
        ]);

        if ($result->getDeletedCount() === 0) {
            throw new NonExistentCallException();
        }
    }
}