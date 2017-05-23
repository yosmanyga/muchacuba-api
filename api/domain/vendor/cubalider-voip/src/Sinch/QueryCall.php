<?php

namespace Cubalider\Voip\Sinch;

use GuzzleHttp\Client;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class QueryCall
{
    /**
     * @var string
     */
    private $appKey;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @param string $appKey
     * @param string $appSecret
     *
     * @di\arguments({
     *     appKey:    "%sinch_app_key%",
     *     appSecret: "%sinch_app_secret%"
     * })
     */
    public function __construct($appKey, $appSecret)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function query($id)
    {
        $response = (new Client)->request(
            'GET',
            sprintf('https://callingapi.sinch.com/v1/calls/id/%s', $id),
            [
                'headers' => [
                    'Authorization' => sprintf(
                        'basic %s',
                        base64_encode(sprintf(
                            'application\%s:%s',
                            $this->appKey,
                            $this->appSecret
                        ))
                    )
                ]
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }
}