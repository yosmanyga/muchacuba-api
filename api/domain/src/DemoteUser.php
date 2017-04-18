<?php

namespace Muchacuba;

use Cubalider\Internet\PickProfile as PickInternetProfile;
use Cubalider\Privilege\NonExistentProfileException as NonExistentPrivilegeProfileException;
use Cubalider\Privilege\Profile\ManageStorage as ManagePrivilegeStorage;
use MongoDB\UpdateResult;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class DemoteUser
{
    /**
     * @var PickInternetProfile
     */
    private $pickInternetProfile;
    
    /**
     * @var ManagePrivilegeStorage
     */
    private $managePrivilegeStorage;

    /**
     * @param PickInternetProfile    $pickInternetProfile
     * @param ManagePrivilegeStorage $managePrivilegeStorage
     */
    public function __construct(
        PickInternetProfile $pickInternetProfile,
        ManagePrivilegeStorage $managePrivilegeStorage
    )
    {
        $this->pickInternetProfile = $pickInternetProfile;
        $this->managePrivilegeStorage = $managePrivilegeStorage;
    }

    /**
     * @param string $email
     * @param string $role
     *
     * @throws NonExistentPrivilegeProfileException
     * @throws \Exception
     */
    public function demote($email, $role)
    {
        try {
            $profile = $this->pickInternetProfile->pick(null, $email);
        } catch (NonExistentPrivilegeProfileException $e) {
            throw $e;
        }

        /** @var UpdateResult $result */
        $result = $this->managePrivilegeStorage->connect()->updateOne(
            ['_id' => $profile->getUniqueness()],
            ['$pull' => ['roles' => $role]]
        );

        if ($result->getMatchedCount() == 0) {
            throw new \Exception();
        }
    }
}
