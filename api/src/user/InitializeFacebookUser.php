<?php

namespace Muchacuba;

use Cubalider\Unique;
use Cubalider\Facebook;
use Cubalider\Privilege;

/**
 * @di\service()
 */
class InitializeFacebookUser
{
    /**
     * @var Unique\CreateUniqueness
     */
    private $createUniqueness;

    /**
     * @var Privilege\InsertProfile
     */
    private $insertPrivilegeProfile;

    /**
     * @var Facebook\InsertProfile
     */
    private $insertFacebookProfile;

    /**
     * @var ListenInitFacebookUser[]
     */
    private $listenServices;

    /**
     * @param Unique\CreateUniqueness  $createUniqueness
     * @param Privilege\InsertProfile  $insertPrivilegeProfile
     * @param Facebook\InsertProfile   $insertFacebookProfile
     * @param ListenInitFacebookUser[] $listenServices
     *
     * @di\arguments({
     *     listenServices: '#cubalider.init_facebook_user'
     * })
     */
    public function __construct(
        Unique\CreateUniqueness $createUniqueness,
        Privilege\InsertProfile $insertPrivilegeProfile,
        Facebook\InsertProfile $insertFacebookProfile,
        array $listenServices
    )
    {
        $this->createUniqueness = $createUniqueness;
        $this->insertPrivilegeProfile = $insertPrivilegeProfile;
        $this->insertFacebookProfile = $insertFacebookProfile;
        $this->listenServices = $listenServices;
    }

    /**
     * @http\resolution({method: "POST", path: "/initialize-facebook-user"})
     * @domain\authorization({roles: []})
     *
     * @param string $guest Id from firebase
     * @param string $id
     * @param string $name
     * @param string $email
     * @param string $picture
     */
    public function initialize(
        $guest,
        $id,
        $name,
        $email,
        $picture
    )
    {
        try {
            $this->createUniqueness->create($guest);
        } catch (Unique\ExistentUniquenessException $e) {
            // Profiles already created
        }

        try {
            $this->insertPrivilegeProfile->insert($guest, []);
        } catch (Privilege\ExistentProfileException $e) {
        }

        try {
            $this->insertFacebookProfile->insert(
                $guest,
                $id,
                $name,
                $email,
                $picture
            );
        } catch (Facebook\ExistentProfileException $e) {
        }

        foreach ($this->listenServices as $listenService) {
            $listenService->listen($guest, $email);
        }
    }
}
