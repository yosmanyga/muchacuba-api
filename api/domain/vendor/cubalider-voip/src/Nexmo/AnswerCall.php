<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\ConnectResponse;
use Cubalider\Voip\HangupResponse;
use Cubalider\Voip\Nexmo\Call\ManageStorage;
use Cubalider\Voip\StartCall as BaseStartCall;
use Cubalider\Voip\UnsupportedResponseException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AnswerCall
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var BaseStartCall
     */
    private $startCall;

    /**
     * @var TranslateConnectResponse
     */
    private $translateConnectResponse;

    /**
     * @var TranslateHangupResponse
     */
    private $translateHangupResponse;

    /**
     * @param ManageStorage            $manageStorage
     * @param BaseStartCall            $startCall
     * @param TranslateConnectResponse $translateConnectResponse
     * @param TranslateHangupResponse  $translateHangupResponse
     */
    public function __construct(
        ManageStorage $manageStorage,
        BaseStartCall $startCall,
        TranslateConnectResponse $translateConnectResponse,
        TranslateHangupResponse $translateHangupResponse
    )
    {
        $this->manageStorage = $manageStorage;
        $this->startCall = $startCall;
        $this->translateConnectResponse = $translateConnectResponse;
        $this->translateHangupResponse = $translateHangupResponse;
    }

    /**
     * @param array $payload
     *
     * @return array
     *
     * @throws UnsupportedResponseException
     */
    public function answer($payload)
    {
        // Insert call as it comes

        $this->manageStorage->connect()->insertOne(new Call(
            $payload['conversation_uuid'],
            Call::STATUS_STARTED,
            $payload
        ));

        $payload = $this->fixVenezuela($payload);

        $response = $this->startCall->start(
            'nexmo',
            $payload['conversation_uuid'],
            $payload['from']
        );

        if ($response instanceof ConnectResponse) {
            $response = $this->translateConnectResponse->translate(
                $response,
                $payload['from']
            );
        } elseif ($response instanceof HangupResponse) {
            $response = $this->translateHangupResponse->translate();
        } else {
            throw new UnsupportedResponseException();
        }

        $this->manageStorage->connect()->updateOne(
            [
                '_id' => $payload['conversation_uuid']
            ],
            [
                '$set' => ['answerResponse' => $response]
            ]
        );

        return $response;
    }

    /**
     * @param array $payload
     * 
     * @return array
     */
    private function fixVenezuela($payload)
    {
        /* Nexmo doesn't add country prefix to calls from Venezuela */
        // TODO: https://github.com/giggsey/libphonenumber-for-php

        if (
            // Call inside Venezuela?
            $payload['to'] == '582123353020'
            // Not having country code?
            && strpos($payload['from'], '+58') === false
        ) {
            // Add country code
            $payload['from'] = sprintf('+58%s', $payload['from']);
        }
        
        return $payload;
    }
}