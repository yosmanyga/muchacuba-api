<?php

namespace Cubalider\Facebook;

use Facebook\Authentication\AccessTokenMetadata;
use Facebook\Facebook as BaseFacebook;
use Facebook\FacebookResponse;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({
 *     private: true
 * })
 */
class QueryApi
{
    /**
     * @var BaseFacebook
     */
    private $facebook;

    /**
     * @param string $apiId
     * @param string $appSecret
     * @param string $defaultGraphVersion
     *
     * @di\arguments({
     *    apiId:                "%fb_app_id%",
     *    $appSecret:           "%fb_app_secret%",
     *    $defaultGraphVersion: "%fb_default_graph_version%"
     * })
     */
    public function __construct($apiId, $appSecret, $defaultGraphVersion)
    {
        $this->facebook = new BaseFacebook([
            'app_id' => $apiId,
            'app_secret' => $appSecret,
            'default_graph_version' => $defaultGraphVersion
        ]);
    }

    /**
     * @param string $endPoint
     * @param string $accessToken
     *
     * @return FacebookResponse
     */
    public function get($endPoint, $accessToken)
    {
        return $this->facebook->get($endPoint, $accessToken);
    }

    /**
     * @param string $token
     *
     * @return AccessTokenMetadata
     */
    public function debugToken($token)
    {
        return $this->facebook->getOAuth2Client()->debugToken($token);
    }

//    /**
//     * @param $name
//     * @param $arguments
//     *
//     * @return mixed
//     */
//    public function __call($name, $arguments)
//    {
//        return call_user_func_array([$this->facebook, $name], $arguments);
//    }
}
