<?php

namespace Cubalider\Voip\Nexmo;

//use Lcobucci\JWT\Builder;
//use Lcobucci\JWT\Signer\Key;
//use Lcobucci\JWT\Signer\Rsa\Sha256;

use Cubalider\Voip\ConnectResponse;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class TranslateHangupResponse
{
    //    /**
//     * @var string
//     */
//    private $privateKey;
//
//    /**
//     * @var string
//     */
//    private $applicationId;
//
//    /**
//     * @param string $privateKey
//     * @param string $applicationId
//     *
//     * @di\arguments({
//     *     privateKey:    "%nexmo_private_key%",
//     *     applicationId: "%nexmo_application_id%"
//     * })
//     */
//    public function __construct($privateKey, $applicationId)
//    {
//        $this->privateKey = $privateKey;
//        $this->applicationId = $applicationId;
//    }

    /**
     * @return array
     */
    public function translate()
    {
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

//    private function generateJWT()
//    {
//        date_default_timezone_set('UTC');
//
//        $jwt = (new Builder())
//            ->setIssuedAt(time() - date('Z'))
//            ->set('application_id', $this->applicationId)
//            ->setId( base64_encode(mt_rand()), true)
//            ->sign(
//                new Sha256(),
//                new Key($this->privateKey))
//            ->getToken();
//
//        return $jwt;
//    }
}