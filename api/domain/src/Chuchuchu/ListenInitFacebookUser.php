<?php

namespace Muchacuba\Chuchuchu;

use Cubalider\Privilege\CreateProfile as CreatePrivilegeProfile;
use Cubalider\Privilege\ExistentProfileException as ExistentPrivilegeProfileException;
use Cubalider\Privilege\Profile\ExistentRoleException as ExistentPrivilegeProfileRoleException;
use Cubalider\Privilege\PromoteProfile as PromotePrivilegeProfile;
use Muchacuba\ListenInitFacebookUser as BaseListenInitFacebookUser;
use Muchacuba\Chuchuchu\CreateProfile as CreateChuchuchuProfile;
use Muchacuba\Chuchuchu\ExistentProfileException as ExistentChuchuchuProfileException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'muchacuba.init_facebook_user',
 *          key: 'chuchuchu'
 *     }]
 * })
 */
class ListenInitFacebookUser implements BaseListenInitFacebookUser
{
    /**
     * @var CreateChuchuchuProfile
     */
    private $createChuchuchuProfile;

    /**
     * @var CreatePrivilegeProfile
     */
    private $createPrivilegeProfile;

    /**
     * @var PromotePrivilegeProfile
     */
    private $promotePrivilegeProfile;

    /**
     * @param CreateChuchuchuProfile  $createChuchuchuProfile
     * @param CreatePrivilegeProfile  $createPrivilegeProfile
     * @param PromotePrivilegeProfile $promotePrivilegeProfile
     */
    public function __construct(
        CreateChuchuchuProfile $createChuchuchuProfile,
        CreatePrivilegeProfile $createPrivilegeProfile,
        PromotePrivilegeProfile $promotePrivilegeProfile
    )
    {
        $this->createChuchuchuProfile = $createChuchuchuProfile;
        $this->createPrivilegeProfile = $createPrivilegeProfile;
        $this->promotePrivilegeProfile = $promotePrivilegeProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($uniqueness, $email)
    {
        /* Create chuchuchu profile or ignore if it already exist */

        try {
            $this->createChuchuchuProfile->create(
                $uniqueness,
                []
            );
        } catch (ExistentChuchuchuProfileException $e) {
        }

        /* Create privilege profile or just add role if profile exists */

        try {
            $this->createPrivilegeProfile->create(
                $uniqueness,
                ['chuchuchu_user']
            );
        } catch (ExistentPrivilegeProfileException $e) {
            try {
                $this->promotePrivilegeProfile->promote(
                    $uniqueness,
                    'chuchuchu_user'
                );
            } catch (ExistentPrivilegeProfileRoleException $e) {
            }
        }
    }
}