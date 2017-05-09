<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\HangupResponse;
use Cubalider\Voip\TranslateResponse;
use Cubalider\Voip\UnsupportedResponseException;
use GuzzleHttp\Client;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

/**
 * @di\service({
 *     deductible: true,
 *     tags: ['cubalider.voip.nexmo.translate_response']
 * })
 */
class TranslateHangupResponse implements TranslateResponse
{
    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $applicationId;

    /**
     * @param string $privateKey
     * @param string $applicationId
     *
     * @di\arguments({
     *     privateKey:    "%nexmo_private_key%",
     *     applicationId: "%nexmo_application_id%"
     * })
     */
    public function __construct($privateKey, $applicationId)
    {
        $this->privateKey = $privateKey;
        $this->applicationId = $applicationId;
    }

    /**
     * {@inheritdoc}
     */
    public function translate($response, $cid, $from)
    {
        if (!$response instanceof HangupResponse) {
            throw new UnsupportedResponseException();
        }

        return [
            [
                'action' => 'talk',
                'text' => 'No disponible',
                'voiceName' => 'Conchita'
            ]
        ];

//        $promise = (new Client())->requestAsync(
//            'PUT',
//            sprintf(
//                'https://api.nexmo.com/v1/calls/%s',
//                $cid
//            ),
//            [
//                'headers' => [
//                    'Content-Type' => 'application/json',
//                    'Authorization' => sprintf('Bearer %s', $this->generateJWT()),
//                ],
//                'json' => [
//                    'action' => 'hangup'
//                ]
//            ]
//        );
//        $promise->then(function ($response) {
//        });
    }

    private function generateJWT()
    {
        date_default_timezone_set('UTC');

        $jwt = (new Builder())
            ->setIssuedAt(time() - date('Z'))
            ->set('application_id', $this->applicationId)
            ->setId( base64_encode(mt_rand()), true)
            ->sign(
                new Sha256(),
                new Key($this->privateKey))
            ->getToken();

        return $jwt;
    }
}