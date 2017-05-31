<?php

namespace Muchacuba\Http;

use Cubalider\Privilege as  PrivilegeProfile;
use Symsonte\Authorization\Role\Collector as BaseCollector;

/**
 * @di\service({
 *     private: true
 * })
 */
class RoleCollector implements BaseCollector
{
    /**
     * @var PrivilegeProfile\PickProfile
     */
    private $pickPrivilegeProfile;

    /**
     * @param PrivilegeProfile\PickProfile $pickPrivilegeProfile
     */
    function __construct(
        PrivilegeProfile\PickProfile $pickPrivilegeProfile
    )
    {
        $this->pickPrivilegeProfile = $pickPrivilegeProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function collect($user)
    {
        try {
            $profile = $this->pickPrivilegeProfile->pick($user);
        } catch (PrivilegeProfile\NonexistentProfileException $e) {
            // If it doesn't have a profile, it means that the user is new
            // and this call was done by init-user

            return [];
        }

        return $profile->getRoles();
    }
}
