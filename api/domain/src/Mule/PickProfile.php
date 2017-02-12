<?php

namespace Muchacuba\Mule;

use Muchacuba\Mule\Profile\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickProfile
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    ) {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $uniqueness
     *
     * @throws NonExistentProfileException
     *
     * @return Profile
     */
    public function pick($uniqueness)
    {
        $criteria = [
            '_id' => $uniqueness,
        ];

        /** @var Profile $profile */
        $profile = $this->manageStorage->connect()
            ->findOne($criteria);

        if (is_null($profile)) {
            throw new NonExistentProfileException();
        }

        return $profile;
    }
}
