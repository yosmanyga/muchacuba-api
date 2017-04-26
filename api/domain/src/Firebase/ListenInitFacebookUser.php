<?php

namespace Muchacuba\Firebase;

use Muchacuba\ListenInitFacebookUser as BaseListenInitFacebookUser;
use Muchacuba\Firebase\CreateProfile as CreateFirebaseProfile;
use Muchacuba\Firebase\ExistentProfileException as ExistentFirebaseProfileException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'muchacuba.init_facebook_user',
 *          key: 'firebase'
 *     }]
 * })
 */
class ListenInitFacebookUser implements BaseListenInitFacebookUser
{
    /**
     * @var CreateFirebaseProfile
     */
    private $createFirebaseProfile;

    /**
     * @param CreateFirebaseProfile $createFirebaseProfile
     */
    public function __construct(
        CreateFirebaseProfile $createFirebaseProfile
    )
    {
        $this->createFirebaseProfile = $createFirebaseProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($uniqueness, $email)
    {
        /* Create firebase profile or ignore if it already exist */

        try {
            $this->createFirebaseProfile->create(
                $uniqueness,
                null
            );
        } catch (ExistentFirebaseProfileException $e) {
        }
    }
}