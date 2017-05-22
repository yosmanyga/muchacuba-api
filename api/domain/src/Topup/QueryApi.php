<?php

namespace Muchacuba\Topup;

use GuzzleHttp\Client;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class QueryApi
{
    /**
     * @var string
     */
    private $apiId;

    /**
     * @var string
     */
    private $apiPassword;

    /**
     * @param string $apiId
     * @param string $apiPassword
     *
     * @di\arguments({
     *     apiId:       "%topup_ding_id%",
     *     apiPassword: "%topup_ding_password%"
     * })
     */
    public function __construct(
        $apiId,
        $apiPassword
    )
    {
        $this->apiId = $apiId;
        $this->apiPassword = $apiPassword;
    }

    /**
     * @param string     $method
     * @param string     $uri
     * @param array|null $params
     *
     * @return array
     */
    public function query($method, $uri, $params = [])
    {
        $response = (new Client())->request(
            'POST',
            'http://www.imoontel.com/yosmy/redir/index.php',
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'method' => $method,
                    'uri' => sprintf('https://edts.ezedistributor.com%s', $uri),
                    'options' => [
                        'headers' => [
                            'Authorization' => sprintf(
                                'Basic %s',
                                base64_encode(sprintf(
                                    '%s:%s',
                                    $this->apiId,
                                    $this->apiPassword
                                ))
                            )
                        ]
                    ]
                ]
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }
}