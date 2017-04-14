<?php

namespace Muchacuba;

use Cubalider\Unique\ExistentUniquenessException;
use Cubalider\Unique\CreateUniqueness;
use Cubalider\Internet\CreateProfile as CreateInternetProfile;
use Cubalider\Facebook\CreateProfile as CreateFacebookProfile;
use Cubalider\Privilege\CreateProfile as CreatePrivilegeProfile;
use Cubalider\Privilege\PickProfile as PickPrivilegeProfile;
use Cubalider\Geo\CreateProfile as CreateGeoProfile;
use Muchacuba\Firebase\CreateProfile as CreateFirebaseProfile;
use Muchacuba\Aloleiro\CreateProfile as CreateAloleiroProfile;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class InitUser
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
     * @var CreateFacebookProfile
     */
    private $createFacebookProfile;

    /**
     * @var CreatePrivilegeProfile
     */
    private $createPrivilegeProfile;

    /**
     * @var CreateGeoProfile
     */
    private $createGeoProfile;

    /**
     * @var CreateFirebaseProfile
     */
    private $createFirebaseProfile;

    /**
     * @var CreateAloleiroProfile
     */
    private $createAloleiroProfile;

    /**
     * @var PickPrivilegeProfile
     */
    private $pickPrivilegeProfile;

    /**
     * @param CreateUniqueness       $createUniqueness
     * @param CreateInternetProfile  $createInternetProfile
     * @param CreateFacebookProfile  $createFacebookProfile
     * @param CreatePrivilegeProfile $createPrivilegeProfile
     * @param CreateGeoProfile       $createGeoProfile
     * @param CreateFirebaseProfile  $createFirebaseProfile
     * @param CreateAloleiroProfile  $createAloleiroProfile
     * @param PickPrivilegeProfile   $pickPrivilegeProfile
     */
    public function __construct(
        CreateUniqueness $createUniqueness,
        CreateInternetProfile $createInternetProfile,
        CreateFacebookProfile $createFacebookProfile,
        CreatePrivilegeProfile $createPrivilegeProfile,
        CreateGeoProfile $createGeoProfile,
        CreateFirebaseProfile $createFirebaseProfile,
        CreateAloleiroProfile $createAloleiroProfile,
        PickPrivilegeProfile $pickPrivilegeProfile
    )
    {
        $this->createUniqueness = $createUniqueness;
        $this->createInternetProfile = $createInternetProfile;
        $this->createFacebookProfile = $createFacebookProfile;
        $this->createPrivilegeProfile = $createPrivilegeProfile;
        $this->createGeoProfile = $createGeoProfile;
        $this->createFirebaseProfile = $createFirebaseProfile;
        $this->createAloleiroProfile = $createAloleiroProfile;
        $this->pickPrivilegeProfile = $pickPrivilegeProfile;
    }

    /**
     * @param string $uniqueness
     * @param array  $facebook   Facebook data
     *
     * @return string[] The roles
     */
    public function init(
        $uniqueness,
        $facebook
    )
    {
        try {
            $this->createUniqueness->create($uniqueness);

            if (!is_null($facebook['email'])) {
                $this->createInternetProfile->create(
                    $uniqueness,
                    $facebook['email']
                );
            }

            $this->createFacebookProfile->create(
                $uniqueness,
                $facebook['id'],
                $facebook['name'],
                $facebook['email'],
                $facebook['picture']
            );

            $this->createPrivilegeProfile->create(
                $uniqueness,
                ['user']
            );

            $this->createGeoProfile->create(
                $uniqueness,
                null,
                null
            );

            $this->createFirebaseProfile->create(
                $uniqueness,
                null
            );

            $this->createAloleiroProfile->create(
                $uniqueness,
                []
            );
        } catch (ExistentUniquenessException $e) {
            // Profiles already created
        }

        return $this->pickPrivilegeProfile->pick($uniqueness)->getRoles();
    }
}
