<?php

namespace Muchacuba;

use Cubalider\Unique\ExistentUniquenessException;
use Cubalider\Unique\CreateUniqueness;
use Cubalider\Privilege\PickProfile as PickPrivilegeProfile;
use Cubalider\Privilege\NonExistentProfileException as NonExistentPrivilegeProfileException;
use Cubalider\Facebook\CreateProfile as CreateFacebookProfile;
use Cubalider\Facebook\ExistentProfileException as ExistentFacebookProfileException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class InitFacebookUser
{
    /**
     * @var CreateUniqueness
     */
    private $createUniqueness;

    /**
     * @var CreateFacebookProfile
     */
    private $createFacebookProfile;

    /**
     * @var PickPrivilegeProfile
     */
    private $pickPrivilegeProfile;

    /**
     * @var ListenInitFacebookUser[]
     */
    private $listenServices;

    /**
     * @param CreateUniqueness         $createUniqueness
     * @param CreateFacebookProfile    $createFacebookProfile
     * @param PickPrivilegeProfile     $pickPrivilegeProfile
     * @param ListenInitFacebookUser[] $listenServices
     *
     * @di\arguments({
     *     listenServices: '#muchacuba.init_facebook_user'
     * })
     */
    public function __construct(
        CreateUniqueness $createUniqueness,
        CreateFacebookProfile $createFacebookProfile,
        PickPrivilegeProfile $pickPrivilegeProfile,
        array $listenServices
    )
    {
        $this->createUniqueness = $createUniqueness;
        $this->createFacebookProfile = $createFacebookProfile;
        $this->pickPrivilegeProfile = $pickPrivilegeProfile;
        $this->listenServices = $listenServices;
    }

    /**
     * @param string $uniqueness This is coming from firebase
     * @param array  $id
     * @param array  $name
     * @param array  $email
     * @param array  $picture
     *
     * @return string[] The roles
     */
    public function init(
        $uniqueness,
        $id,
        $name,
        $email,
        $picture
    )
    {
        try {
            $this->createUniqueness->create($uniqueness);
        } catch (ExistentUniquenessException $e) {
            // Profiles already created
        }

        try {
            $this->createFacebookProfile->create(
                $uniqueness,
                $id,
                $name,
                $email,
                $picture
            );
        } catch (ExistentFacebookProfileException $e) {
        }

        foreach ($this->listenServices as $listenService) {
            $listenService->listen($uniqueness, $email);
        }

        try {
            $privilegeProfile = $this->pickPrivilegeProfile->pick($uniqueness);
        } catch (NonExistentPrivilegeProfileException $e) {
            return [];
        }

        return $privilegeProfile->getRoles();
    }
}
