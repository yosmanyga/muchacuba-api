<?php

namespace Muchacuba;

use Cubalider\Unique\ExistentUniquenessException;
use Cubalider\Unique\CreateUniqueness;
use Cubalider\Privilege\PickProfile as PickPrivilegeProfile;
use Cubalider\Privilege\NonExistentProfileException as NonExistentPrivilegeProfileException;

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
     * @var PickPrivilegeProfile
     */
    private $pickPrivilegeProfile;

    /**
     * @var ListenInitFacebookUser[]
     */
    private $listenServices;

    /**
     * @param CreateUniqueness         $createUniqueness
     * @param PickPrivilegeProfile     $pickPrivilegeProfile
     * @param ListenInitFacebookUser[] $listenServices
     *
     * @di\arguments({
     *     listenServices: '#muchacuba.init_facebook_user'
     * })
     */
    public function __construct(
        CreateUniqueness $createUniqueness,
        PickPrivilegeProfile $pickPrivilegeProfile,
        array $listenServices
    )
    {
        $this->createUniqueness = $createUniqueness;
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
