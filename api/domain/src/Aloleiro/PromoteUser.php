<?php

namespace Muchacuba\Aloleiro;

use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Profile\ManageStorage;
use Muchacuba\PromoteUser as BasePromoteUser;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PromoteUser
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var CreateProfile
     */
    private $createProfile;

    /**
     * @var BasePromoteUser
     */
    private $promoteUser;

    /**
     * @param ManageStorage   $manageStorage
     * @param CreateProfile   $createProfile
     * @param BasePromoteUser $promoteUser
     */
    public function __construct(
        ManageStorage $manageStorage,
        CreateProfile $createProfile,
        BasePromoteUser $promoteUser
    )
    {
        $this->manageStorage = $manageStorage;
        $this->createProfile = $createProfile;
        $this->promoteUser = $promoteUser;
    }

    /**
     * @param string $uniqueness
     * @param string $business
     * @param string $role
     */
    public function promote($uniqueness, $business, $role)
    {
        /** @var UpdateResult $result */
        $result = $this->manageStorage->connect()->updateOne(
            ['_id' => $uniqueness],
            ['$set' => ['business' => $business]]
        );

        if ($result->getModifiedCount() == 0) {
            $this->createProfile->create($uniqueness, $business);
        }

        $this->promoteUser->promote($uniqueness, $role);
    }
}
