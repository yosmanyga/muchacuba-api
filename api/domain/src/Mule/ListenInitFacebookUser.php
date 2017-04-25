<?php

namespace Muchacuba\Mule;

use Muchacuba\ListenInitFacebookUser as BaseListenInitFacebookUser;
use Cubalider\Privilege\PromoteProfile as PromotePrivilegeProfile;
use Cubalider\Privilege\CreateProfile as CreatePrivilegeProfile;
use Cubalider\Privilege\ExistentProfileException as ExistentPrivilegeProfileException;
use Cubalider\Privilege\Profile\ExistentRoleException as ExistentPrivilegeProfileRoleException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'muchacuba.init_facebook_user',
 *          key: 'mule'
 *     }]
 * })
 */
class ListenInitFacebookUser implements BaseListenInitFacebookUser
{
    /**
     * @var CreatePrivilegeProfile
     */
    private $createPrivilegeProfile;

    /**
     * @var PromotePrivilegeProfile
     */
    private $promotePrivilegeProfile;

    /**
     * @param CreatePrivilegeProfile  $createPrivilegeProfile
     * @param PromotePrivilegeProfile $promotePrivilegeProfile
     */
    public function __construct(
        CreatePrivilegeProfile $createPrivilegeProfile,
        PromotePrivilegeProfile $promotePrivilegeProfile
    )
    {
        $this->createPrivilegeProfile = $createPrivilegeProfile;
        $this->promotePrivilegeProfile = $promotePrivilegeProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($uniqueness, $email)
    {
        /* Create privilege profile or just add role if profile exists */

        try {
            $this->createPrivilegeProfile->create(
                $uniqueness,
                ['mule_user']
            );
        } catch (ExistentPrivilegeProfileException $e) {
            try {
                $this->promotePrivilegeProfile->promote(
                    $uniqueness,
                    'mule_user'
                );
            } catch (ExistentPrivilegeProfileRoleException $e) {
            }
        }
    }
}