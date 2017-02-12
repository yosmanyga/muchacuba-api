<?php

namespace Muchacuba\Chuchuchu;

use Cubalider\Internet\Profile as InternetProfile;
use Cubalider\Internet\Profile\ManageStorage as InternetManageStorage;
use Cubalider\Facebook\Profile as FacebookProfile;
use Cubalider\Facebook\Profile\ManageStorage as FacebookManageStorage;
use Muchacuba\Chuchuchu\Profile\ManageStorage as ChuchuchuManageStorage;

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
     * @var FacebookManageStorage
     */
    private $facebookManageStorage;

    /**
     * @var InternetManageStorage
     */
    private $internetManageStorage;

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
     * @return Contact[]
     */
    public function collect($uniqueness)
    {
        /** @var Profile $profile */
        $profile = $this->chuchuchuManageStorage->connect()
            ->findOne([
                '_id' => $uniqueness
            ]);

        $contacts = [];

        // Populate contacts using internet profile

        /** @var InternetProfile[] $internetProfiles */
        $internetProfiles = $this->internetManageStorage->connect()->find([
            '_id' => ['$in' => $profile->getContacts()]
        ]);
        foreach ($internetProfiles as $internetProfile) {
            $contacts[$internetProfile->getUniqueness()] = new Contact(
                $internetProfile->getUniqueness(),
                $internetProfile->getEmail(),
                null
            );
        }

        // Override with facebook profile

        /** @var FacebookProfile[] $facebookProfiles */
        $facebookProfiles = $this->facebookManageStorage->connect()->find([
            '_id' => ['$in' => $profile->getContacts()]
        ]);
        foreach ($facebookProfiles as $facebookProfile) {
            $contacts[$facebookProfile->getUniqueness()] = new Contact(
                $facebookProfile->getUniqueness(),
                $facebookProfile->getName(),
                $facebookProfile->getPicture()
            );
        }

        return array_values($contacts);
    }
}
