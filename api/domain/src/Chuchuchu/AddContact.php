<?php

namespace Muchacuba\Chuchuchu;

use Muchacuba\Chuchuchu\Profile\ManageStorage as ManageChuchuchuStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class AddContact
{
    /**
     * @var ManageChuchuchuStorage
     */
    private $manageChuchuchuStorage;

    /**
     * @var CreateProfile
     */
    private $createProfile;

    /**
     * @param ManageChuchuchuStorage $manageChuchuchuStorage
     * @param CreateProfile          $createProfile
     */
    public function __construct(
        ManageChuchuchuStorage $manageChuchuchuStorage,
        CreateProfile $createProfile
    )
    {
        $this->manageChuchuchuStorage = $manageChuchuchuStorage;
        $this->createProfile = $createProfile;
    }

    /**
     * @param string $uniqueness
     * @param string $contact
     */
    public function add($uniqueness, $contact)
    {
        $profile = $this->manageChuchuchuStorage->connect()->findOne([
            '_id' => $uniqueness,
            'contacts' => ['$in' => [$contact]]
        ]);

        if (is_null($profile)) {
            $this->manageChuchuchuStorage->connect()->updateOne(
                ['_id' => $uniqueness],
                ['$push' => ['contacts' => $contact]]
            );
        }
    }
}