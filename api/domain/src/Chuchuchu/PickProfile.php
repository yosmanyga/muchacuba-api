<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Profile\ManageStorage;

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
     * @return Profile
     *
     * @throws NonExistentProfileException
     */
    public function pick($uniqueness)
    {
        /** @var Profile $profile */
        $profile = $this->manageStorage->connect()
            ->findOne([
                '_id' => $uniqueness
            ]);

        if (is_null($profile)) {
            throw new NonExistentProfileException();
        }

        return $profile;
    }
}
