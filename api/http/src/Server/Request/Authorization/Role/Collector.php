<?php

namespace Muchacuba\Http\Server\Request\Authorization\Role;

use Cubalider\Privilege\NonExistentProfileException as NonExistentPrivilegeProfileException;
use Cubalider\Privilege\PickProfile as PickPrivilegeProfile;
use Symsonte\Http\Server\Request\Authorization\Role\Collector as BaseCollector;

/**
 * @di\service({
 *     private: true
 * })
 */
class Collector implements BaseCollector
{
    /**
     * @var PickPrivilegeProfile
     */
    private $pickPrivilegeProfile;

    /**
     * @param PickPrivilegeProfile $pickPrivilegeProfile
     *
     * @di\arguments({
     *     pickPrivilegeProfile: '@cubalider.privilege.pick_profile'
     * })
     */
    function __construct(
        PickPrivilegeProfile $pickPrivilegeProfile
    )
    {
        $this->pickPrivilegeProfile = $pickPrivilegeProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function collect($uniqueness)
    {
        try {
            $profile = $this->pickPrivilegeProfile->pick($uniqueness);
        } catch (NonExistentPrivilegeProfileException $e) {
            // If it doesn't have a profile, it means that the user is new
            // and this call was done by init-user

            return [];
        }

        return $profile->getRoles();
    }
}
