<?php

namespace Muchacuba\Chuchuchu;

use Cubalider\Facebook\Profile as FacebookProfile;
use Cubalider\Facebook\Profile\ManageStorage as FacebookManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class EnrichUsers
{
    /**
     * @var FacebookManageStorage
     */
    private $facebookManageStorage;

    /**
     * @param FacebookManageStorage $facebookManageStorage
     */
    public function __construct(FacebookManageStorage $facebookManageStorage)
    {
        $this->facebookManageStorage = $facebookManageStorage;
    }

    /**
     * @param string[] $users
     *
     * @return User[]
     *
     * @throws UnauthorizedException
     */
    public function enrich($users)
    {
        /** @var FacebookProfile[] $facebookProfiles */
        $facebookProfiles = $this->facebookManageStorage->connect()->find([
            '_id' => ['$in' => $users]
        ]);
        
        $users = [];
        
        foreach ($facebookProfiles as $facebookProfile) {
            $users[$facebookProfile->getUniqueness()] = new User(
                $facebookProfile->getUniqueness(),
                $facebookProfile->getName(),
                $facebookProfile->getPicture()
            );
        }

        // Need array_values to return non-associative array
        // Otherwise it's converted to an object by json
        return array_values($users);
    }
}
