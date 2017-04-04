<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Profile\ManageStorage as ChuchuchuManageStorage;
use Muchacuba\User;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectContacts
{
    /**
     * @var ChuchuchuManageStorage
     */
    private $chuchuchuManageStorage;



    /**
     * @param ChuchuchuManageStorage $chuchuchuManageStorage
     * @param FacebookManageStorage  $facebookManageStorage
     * @param InternetManageStorage  $internetManageStorage
     */
    public function __construct(
        ChuchuchuManageStorage $chuchuchuManageStorage,
        FacebookManageStorage $facebookManageStorage,
        InternetManageStorage $internetManageStorage
    )
    {
        $this->chuchuchuManageStorage = $chuchuchuManageStorage;
        $this->facebookManageStorage = $facebookManageStorage;
        $this->internetManageStorage = $internetManageStorage;
    }

    /**
     * @param string $uniqueness
     *
     * @return User[]
     */
    public function collect($uniqueness)
    {
        /** @var Profile $profile */
        $profile = $this->chuchuchuManageStorage->connect()
            ->findOne([
                '_id' => $uniqueness
            ]);

        /** @var User[] $contacts */
        $contacts = [];

        // Populate contacts using internet profile

        /** @var InternetProfile[] $internetProfiles */
        $internetProfiles = $this->internetManageStorage->connect()->find([
            '_id' => ['$in' => $profile->getContacts()]
        ]);
        foreach ($internetProfiles as $internetProfile) {
            $contacts[$internetProfile->getUniqueness()] = new User(
                $internetProfile->getUniqueness(),
                null,
                $internetProfile->getEmail(),
                null,
                null
            );
        }

        // Override with facebook profile




    }
}
