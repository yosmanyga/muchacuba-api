<?php

namespace Muchacuba\Internauta\Advertising;

use Muchacuba\Internauta\Advertising\Email\ManageStorage as EmailManageStorage;

/**
 * @di\service()
 */
class AdvertiseUsers
{
    /**
     * @var EmailManageStorage
     */
    private $emailManageStorage;

    /**
     * @var ResolveProfiles
     */
    private $resolveProfiles;

    /**
     * @var AdvertiseUser
     */
    private $advertiseUser;

    /**
     * @param EmailManageStorage $emailManageStorage
     * @param ResolveProfiles    $resolveProfiles
     * @param AdvertiseUser      $advertiseUser
     */
    public function __construct(
        EmailManageStorage $emailManageStorage,
        ResolveProfiles $resolveProfiles,
        AdvertiseUser $advertiseUser
    )
    {
        $this->emailManageStorage = $emailManageStorage;
        $this->resolveProfiles = $resolveProfiles;
        $this->advertiseUser = $advertiseUser;
    }

    /**
     * @param int $amount
     *
     * @return int
     */
    public function advertise($amount)
    {
        /** @var Email[] $emails */
        $emails = $this->emailManageStorage->connect()->find();

        $i = 0;
        foreach ($emails as $email) {
            /** @var Profile[] $profiles */
            $profiles = $this->resolveProfiles->resolve($email->getId(), $amount);

            foreach ($profiles as $profile) {
                $this->advertiseUser->advertise($profile, $email);

                $i++;
            }

            if ($i == $amount) {
                break;
            }
        }

        return $i;
    }
}