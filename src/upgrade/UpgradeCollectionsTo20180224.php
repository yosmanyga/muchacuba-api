<?php

namespace Muchacuba;

use Muchacuba\UpgradeCollectionsTo20180224\UpdateUserProfilesCollections;


/**
 * @di\service({
 *   tags: [{
 *     name: 'muchacuba.upgrade_collections',
 *     key: '20180224'
 *   }]
 * })
 */
class UpgradeCollectionsTo20180224 implements UpgradeCollections
{
    /**
     * @var UpdateUserProfilesCollections
     */
    private $updateUserProfilesCollections;

    /**
     * @param UpdateUserProfilesCollections $updateUserProfilesCollections
     */
    public function __construct(
        UpdateUserProfilesCollections $updateUserProfilesCollections
    ) {
        $this->updateUserProfilesCollections = $updateUserProfilesCollections;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade($last)
    {
        if ($last !== null) {
            return false;
        }

        $this->updateUserProfilesCollections->update();

        return true;
    }
}