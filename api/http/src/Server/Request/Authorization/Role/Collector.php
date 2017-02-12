<?php

namespace Muchacuba\Http\Server\Request\Authorization\Role;

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
        $profile = $this->pickProfile->pick($uniqueness);

        return $profile->getRoles();
    }
}
