<?php

namespace Muchacuba;

use Cubalider\Internet\CreateProfile as CreateInternetProfile;
use Cubalider\Privilege\CreateProfile as CreatePrivilegeProfile;
use Cubalider\Facebook\CreateProfile as CreateFacebookProfile;
use Muchacuba\Chuchuchu\CreateProfile as CreateChuchuchuProfile;
use Cubalider\Unique\CreateUniqueness;
use Muchacuba\Chuchuchu\ExistentProfileException as ExistentChuchuchuProfileException;
use Muchacuba\Chuchuchu\Firebase\CreateProfile as CreateFirebaseProfile;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CreateProfiles
{
    /**
     * @var CreateUniqueness
     */
    private $createUniqueness;
    
    /**
     * @var CreateInternetProfile
     */
    private $createInternetProfile;

    /**
     * @var CreatePrivilegeProfile
     */
    private $createPrivilegeProfile;

    /**
     * @var CreateFacebookProfile;
     */
    private $createFacebookProfile;

    /**
     * @var CreateChuchuchuProfile
     */
    private $createChuchuchuProfile;

    /**
     * @var CreateFirebaseProfile
     */
    private $createFirebaseProfile;
    
    /**
     * @param CreateUniqueness       $createUniqueness
     * @param CreateInternetProfile  $createInternetProfile
     * @param CreatePrivilegeProfile $createPrivilegeProfile
     * @param CreateFacebookProfile  $createFacebookProfile
     * @param CreateChuchuchuProfile $createChuchuchuProfile
     * @param CreateFirebaseProfile  $createFirebaseProfile
     */
    public function __construct(
        CreateUniqueness $createUniqueness,
        CreateInternetProfile $createInternetProfile,
        CreatePrivilegeProfile $createPrivilegeProfile,
        CreateFacebookProfile $createFacebookProfile,
        CreateChuchuchuProfile $createChuchuchuProfile,
        CreateFirebaseProfile $createFirebaseProfile
    )
    {
        $this->createUniqueness = $createUniqueness;
        $this->createInternetProfile = $createInternetProfile;
        $this->createPrivilegeProfile = $createPrivilegeProfile;
        $this->createFacebookProfile = $createFacebookProfile;
        $this->createChuchuchuProfile = $createChuchuchuProfile;
        $this->createFirebaseProfile = $createFirebaseProfile;
    }

    /**
     * @param string $uniqueness
     * @param array  $internetData
     * @param array  $privilegeData
     * @param array  $facebookData
     * @param array  $chuchuchuData
     * @param array  $firebaseData
     *
     * @return string
     */
    public function create(
        $uniqueness,
        $internetData,
        $privilegeData,
        $facebookData,
        $chuchuchuData,
        $firebaseData
    )
    {
        if (is_null($uniqueness)) {
            $uniqueness = $this->createUniqueness->create();
        }

        if (!is_null($internetData)) {
            $this->createInternetProfile->create(
                $uniqueness, 
                $internetData['email']
            );
        }
        
        if (!is_null($privilegeData)) {
            $this->createPrivilegeProfile->create(
                $uniqueness,
                $privilegeData['roles']
            );
        }

        if (!is_null($facebookData)) {
            $this->createFacebookProfile->create(
                $uniqueness,
                $facebookData['id'],
                $facebookData['name'],
                isset($facebookData['email']) ? $facebookData['email'] : null,
                isset($facebookData['picture']) ? $facebookData['picture'] : null
            );
        }

        if (!is_null($chuchuchuData)) {
            try {
                $this->createChuchuchuProfile->create(
                    $uniqueness,
                    $chuchuchuData['contacts']
                );
            } catch (ExistentChuchuchuProfileException $e) {
            }
        }

        if (!is_null($firebaseData)) {
            $this->createFirebaseProfile->create(
                $uniqueness,
                $firebaseData['token']
            );
        }
        
        return $uniqueness;
    }
}
