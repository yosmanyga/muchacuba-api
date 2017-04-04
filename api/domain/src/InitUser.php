<?php

namespace Muchacuba;

use Cubalider\Unique\ExistentUniquenessException;
use Cubalider\Unique\CreateUniqueness;
use Cubalider\Internet\CreateProfile as CreateInternetProfile;
use Cubalider\Facebook\CreateProfile as CreateFacebookProfile;
use Cubalider\Privilege\CreateProfile as CreatePrivilegeProfile;
use Cubalider\Geo\CreateProfile as CreateGeoProfile;
use Muchacuba\Firebase\CreateProfile as CreateFirebaseProfile;

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
     * @param CreateUniqueness       $createUniqueness
     * @param CreateInternetProfile  $createInternetProfile
     * @param CreateFacebookProfile  $createFacebookProfile
     * @param CreatePrivilegeProfile $createPrivilegeProfile
     * @param CreateGeoProfile       $createGeoProfile
     * @param CreateFirebaseProfile  $createFirebaseProfile
     */
    public function __construct(
        CreateUniqueness $createUniqueness,
        CreateInternetProfile $createInternetProfile,
        CreateFacebookProfile $createFacebookProfile,
        CreatePrivilegeProfile $createPrivilegeProfile,
        CreateGeoProfile $createGeoProfile,
        CreateFirebaseProfile $createFirebaseProfile
    )
    {
        $this->createUniqueness = $createUniqueness;
        $this->createInternetProfile = $createInternetProfile;
        $this->createFacebookProfile = $createFacebookProfile;
        $this->createPrivilegeProfile = $createPrivilegeProfile;
        $this->createGeoProfile = $createGeoProfile;
        $this->createFirebaseProfile = $createFirebaseProfile;
    }

    /**
     * @param string $uniqueness
     * @param array  $facebook   Facebook data
     */
    public function init(
        $uniqueness,
        $facebook
    )
    {
        try {
            $this->createUniqueness->create($uniqueness);
        } catch (ExistentUniquenessException $e) {
            // Profiles already created

            return;
        }

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
    }
}
