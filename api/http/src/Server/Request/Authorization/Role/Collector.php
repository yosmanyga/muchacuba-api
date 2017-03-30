<?php

namespace Muchacuba\Http\Server\Request\Authorization\Role;

use Cubalider\Privilege\NonExistentProfileException;
use Cubalider\Privilege\PickProfile;
use Symsonte\Http\Server\Request\Authorization\Role\Collector as BaseCollector;

/**
 * @di\service({
 *     private: true
 * })
 */
class Collector implements BaseCollector
{
    /**
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @param PickProfile $pickProfile
     *
     * @di\arguments({
     *     pickProfile: '@cubalider.privilege.pick_profile'
     * })
     */
    function __construct(
        PickProfile $pickProfile
    )
    {
        $this->pickProfile = $pickProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function collect($uniqueness)
    {
        try {
            $profile = $this->pickProfile->pick($uniqueness);
        } catch (NonExistentProfileException $e) {
            // If it doesn't have a profile, it means that the user is new
            // and this call was done by init-profile

            return ['user'];
        }

        return $profile->getRoles();
    }
}
