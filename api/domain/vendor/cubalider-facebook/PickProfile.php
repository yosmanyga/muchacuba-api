<?php

namespace Cubalider\Facebook;

use Cubalider\Facebook\Profile\ManageStorage;

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
     * Picks a profile with given criteria.
     *
     * @param string|null $uniqueness
     * @param string|null $id
     *
     * @throws NonExistentProfileException
     *
     * @return Profile
     */
    public function pick($uniqueness = null, $id = null)
    {
        $criteria = [];

        if (!is_null($uniqueness)) {
            $criteria['_id'] = $uniqueness;
        }

        if (!is_null($id)) {
            $criteria['id'] = $id;
        }

        /** @var Profile $profile */
        $profile = $this->manageStorage->connect()
            ->findOne($criteria);

        if (is_null($profile)) {
            throw new NonExistentProfileException($uniqueness);
        }

        return $profile;
    }
}
