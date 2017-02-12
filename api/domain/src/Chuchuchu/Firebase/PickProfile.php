<?php

namespace Muchacuba\Chuchuchu\Firebase;

use Muchacuba\Chuchuchu\Firebase\Profile\ManageStorage;

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
     */
    public function pick($uniqueness)
    {
        /** @var Profile $profile */
        $profile = $this->manageStorage->connect()
            ->findOne([
                '_id' => $uniqueness
            ]);

        return $profile;
    }
}
