<?php

namespace Muchacuba\Http;

use Yosmy\Privilege;
use Symsonte\Authorization\Role\Collector as BaseCollector;

/**
 * @di\service({
 *     private: true
 * })
 */
class RoleCollector implements BaseCollector
{
    /**
     * @var Privilege\PickProfile
     */
    private $pickPrivilegeProfile;

    /**
     * @param Privilege\PickProfile $pickPrivilegeProfile
     */
    function __construct(
        Privilege\PickProfile $pickPrivilegeProfile
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
        } catch (Privilege\Profile\NonexistentException $e) {
            // If it doesn't have a profile, it means that the user is new
            // and this call was done by init-user

            return [];
        }

        return $profile->getRoles();
    }
}
