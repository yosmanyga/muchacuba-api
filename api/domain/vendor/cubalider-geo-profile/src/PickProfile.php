<?php

namespace Cubalider\Geo;

use Cubalider\Geo\Profile\ManageStorage;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({deductible: true})
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
    public function __construct(ManageStorage $manageStorage)
    {
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
        /** @var Profile $profile */
        $profile = $this->manageStorage->connect()
            ->findOne(['_id' => $uniqueness]);

        if (is_null($profile)) {
            throw new NonExistentProfileException();
        }

        return $profile;
    }
}
