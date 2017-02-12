<?php

namespace Muchacuba;

use Cubalider\Facebook\InvalidTokenException as InvalidFacebookTokenException;
use Lcobucci\JWT\Builder;
use Cubalider\Internet\PickProfile as PickInternetProfile;
use Cubalider\Internet\NonExistentProfileException as NonExistentInternetProfileException;
use Cubalider\Facebook\Authenticate as FacebookAuthenticate;
use Cubalider\Facebook\PickProfile as PickFacebookProfile;
use Cubalider\Facebook\NonExistentProfileException as NonExistentFacebookProfileException;

/**
 * @di\service({deductible: true})
 */
class Authenticate
{
    /**
     * @var FacebookAuthenticate
     */
    private $facebookAuthenticate;

    /**
     * @var PickFacebookProfile
     */
    private $pickFacebookProfile;

    /**
     * @var PickInternetProfile
     */
    private $pickInternetProfile;

    /**
     * @var CreateProfiles
     */
    private $createProfiles;

    /**
     * @param FacebookAuthenticate $facebookAuthenticate
     * @param PickFacebookProfile  $pickFacebookProfile
     * @param PickInternetProfile  $pickInternetProfile
     * @param CreateProfiles       $createProfiles
     */
    public function __construct(
        FacebookAuthenticate $facebookAuthenticate,
        PickFacebookProfile $pickFacebookProfile,
        PickInternetProfile $pickInternetProfile,
        CreateProfiles $createProfiles
    )
    {
        $this->facebookAuthenticate = $facebookAuthenticate;
        $this->pickFacebookProfile = $pickFacebookProfile;
        $this->pickInternetProfile = $pickInternetProfile;
        $this->createProfiles = $createProfiles;
    }

    /**
     * @param string $token
     *
     * @return string
     *
     * @throws InvalidTokenException
     */
    public function authenticate($token)
    {
        try {
            $facebookData = $this->facebookAuthenticate->authenticate($token);
        } catch (InvalidFacebookTokenException $e) {
            throw new InvalidTokenException();
        }

        try {
            $facebookProfile = $this->pickFacebookProfile->pick(null, $facebookData['id']);

            $uniqueness = $facebookProfile->getUniqueness();
        } catch (NonExistentFacebookProfileException $e) {
            $email = isset($facebookData['email']) ? $facebookData['email'] : null;

            // Got email from facebook?
            if (!is_null($email)) {
                // Try to find an internet profile with that email
                try {
                    $internetProfile = $this->pickInternetProfile->pick(null, $email);

                    $uniqueness = $internetProfile->getUniqueness();

                    $this->createProfiles->create(
                        $uniqueness,
                        null,
                        ['roles' => ['user']],
                        $facebookData,
                        ['contacts' => [], 'conversations' => []],
                        ['token' => '']
                    );
                } catch (NonExistentInternetProfileException $e) {
                    $uniqueness = $this->createProfiles->create(
                        null,
                        ['email' => $email],
                        ['roles' => ['user']],
                        $facebookData,
                        ['contacts' => [], 'conversations' => []],
                        ['token' => '']
                    );
                }
            } else {
                $uniqueness = $this->createProfiles->create(
                    null,
                    null,
                    ['roles' => ['user']],
                    $facebookData,
                    ['contacts' => [], 'conversations' => []],
                    ['token' => '']
                );
            }
        }

        return $this->createToken($uniqueness);
    }

    /**
     * @param string $uniqueness
     *
     * @return string
     */
    private function createToken($uniqueness)
    {
        $time = time();

        $token = (new Builder())
            ->setIssuer('http://muchacuba.com')
            ->setAudience('http://muchacuba.com')
            ->setId('mUch@cub417', true)
            ->setIssuedAt($time)
            ->setNotBefore($time)
            ->setExpiration($time + 86400)
            ->set('uid', $uniqueness)
            ->getToken();

        return (string) $token;
    }
}
