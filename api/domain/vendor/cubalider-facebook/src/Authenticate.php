<?php

namespace Cubalider\Facebook;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookResponse;
use Cubalider\Facebook\PickProfile as PickFacebookProfile;

/**
 * @di\service({deductible: true})
 */
class Authenticate
{
    /**
     * @var QueryApi
     */
    private $queryApi;

    /**
     * @var PickFacebookProfile
     */
    private $pickFacebookProfile;

    /**
     * @param QueryApi    $queryApi
     * @param PickProfile $pickFacebookProfile
     */
    public function __construct(
        QueryApi $queryApi,
        PickProfile $pickFacebookProfile
    )
    {
        $this->queryApi = $queryApi;
        $this->pickFacebookProfile = $pickFacebookProfile;
    }

    /**
     * @param string $token
     *
     * @return array
     *
     * @throws InvalidTokenException
     */
    public function authenticate($token)
    {
        /* Get data from facebook */

        try {
            /** @var FacebookResponse $response */
            $response = $this->queryApi->get('/me?fields=name,email,picture', $token);

            $picture = $response->getGraphUser()->getPicture();
        } catch (FacebookSDKException $e) {
            throw new InvalidTokenException();
        }

        return array_merge(
            $response->getDecodedBody(),
            ['picture' => $picture->getUrl()]
        );
    }
}
