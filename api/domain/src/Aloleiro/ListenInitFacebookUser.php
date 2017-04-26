<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Privilege\Profile\ExistentRoleException;
use Muchacuba\ListenInitFacebookUser as BaseListenInitFacebookUser;
use Muchacuba\Aloleiro\CreateProfile as CreateAloleiroProfile;
use Cubalider\Privilege\PromoteProfile as PromotePrivilegeProfile;
use Cubalider\Privilege\CreateProfile as CreatePrivilegeProfile;
use Cubalider\Privilege\ExistentProfileException as ExistentPrivilegeProfileException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'muchacuba.init_facebook_user',
 *          key: 'aloleiro'
 *     }]
 * })
 */
class ListenInitFacebookUser implements BaseListenInitFacebookUser
{
    /**
     * @var PickApproval
     */
    private $pickApproval;

    /**
     * @var RemoveApproval
     */
    private $removeApproval;

    /**
     * @var PickAdminApproval
     */
    private $pickAdminApproval;

    /**
     * @var RemoveAdminApproval
     */
    private $removeAdminApproval;
    
    /**
     * @var CreatePrivilegeProfile
     */
    private $createPrivilegeProfile;

    /**
     * @var CreateAloleiroProfile
     */
    private $createAloleiroProfile;

    /**
     * @var PromotePrivilegeProfile
     */
    private $promotePrivilegeProfile;

    /**
     * @param PickApproval            $pickApproval
     * @param RemoveApproval          $removeApproval
     * @param PickAdminApproval       $pickAdminApproval
     * @param RemoveAdminApproval     $removeAdminApproval
     * @param CreatePrivilegeProfile  $createPrivilegeProfile
     * @param CreateProfile           $createAloleiroProfile
     * @param PromotePrivilegeProfile $promotePrivilegeProfile
     */
    public function __construct(
        PickApproval $pickApproval,
        RemoveApproval $removeApproval,
        PickAdminApproval $pickAdminApproval,
        RemoveAdminApproval $removeAdminApproval,
        CreatePrivilegeProfile $createPrivilegeProfile,
        CreateProfile $createAloleiroProfile,
        PromotePrivilegeProfile $promotePrivilegeProfile
    )
    {
        $this->pickApproval = $pickApproval;
        $this->removeApproval = $removeApproval;
        $this->pickAdminApproval = $pickAdminApproval;
        $this->removeAdminApproval = $removeAdminApproval;
        $this->createPrivilegeProfile = $createPrivilegeProfile;
        $this->createAloleiroProfile = $createAloleiroProfile;
        $this->promotePrivilegeProfile = $promotePrivilegeProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($uniqueness, $email)
    {
        $this->manageApprovals($uniqueness, $email);

        $this->manageAdminApprovals($uniqueness, $email);
    }

    /**
     * @param string $uniqueness
     * @param string $email
     */
    private function manageApprovals($uniqueness, $email)
    {
        try {
            $approval = $this->pickApproval->pick($email);
        } catch (NonExistentApprovalException $e) {
            // Need an approval to get a profile with roles
            return;
        }

        /* Create privilege profile or just add roles if exists */

        try {
            $this->createPrivilegeProfile->create(
                $uniqueness,
                $approval->getRoles()
            );
        } catch (ExistentPrivilegeProfileException $e) {
            foreach ($approval->getRoles() as $role) {
                try {
                    $this->promotePrivilegeProfile->promote(
                        $uniqueness,
                        $role
                    );
                } catch (ExistentRoleException $e) {
                }
            }
        }

        /* Create aloleiro profile */

        $this->createAloleiroProfile->create(
            $uniqueness,
            $approval->getBusiness()
        );

        /* Remove approval after finish */

        $this->removeApproval->remove($email);
    }

    /**
     * @param string $uniqueness
     * @param string $email
     */
    private function manageAdminApprovals($uniqueness, $email)
    {
        try {
            $adminApproval = $this->pickAdminApproval->pick($email);
        } catch (NonExistentAdminApprovalException $e) {
            return;
        }

        /* Create privilege profile or just add roles if exists */

        try {
            $this->createPrivilegeProfile->create(
                $uniqueness,
                [$adminApproval->getRole()]
            );
        } catch (ExistentPrivilegeProfileException $e) {
            try {
                $this->promotePrivilegeProfile->promote(
                    $uniqueness,
                    $adminApproval->getRole()
                );
            } catch (ExistentRoleException $e) {
            }
        }

        /* Remove adminApproval after finish */

        $this->removeAdminApproval->remove($email);
    }
}