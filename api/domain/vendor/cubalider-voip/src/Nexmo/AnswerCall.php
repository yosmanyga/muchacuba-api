<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\ConnectResponse;
use Cubalider\Voip\HangupResponse;
use Cubalider\Voip\Nexmo\Call\ManageStorage;
use Cubalider\Voip\StartCall as BaseStartCall;
use Cubalider\Voip\TranslateResponse;
use Cubalider\Voip\UnsupportedResponseException;
use MongoDB\BSON\UTCDateTime;

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
     * @var TranslateResponse[]
     */
    private $translateResponseServices;
    
    /**
     * @param ManageStorage       $manageStorage
     * @param BaseStartCall       $startCall
     * @param TranslateResponse[] $translateResponseServices
     * 
     * @di\arguments({
     *     translateResponseServices: '#cubalider.voip.nexmo.translate_response'
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        BaseStartCall $startCall,
        array $translateResponseServices
    )
    {
        $this->manageStorage = $manageStorage;
        $this->startCall = $startCall;
        $this->translateResponseServices = $translateResponseServices;
    }

    /**
     * @param array $payload
     *
     * @return array
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

        $response = $this->translateResponse(
            $response,
            $payload['conversation_uuid'],
            $payload['from']
        );
        
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

    /**
     * @param ConnectResponse|HangupResponse $response
     * @param string                         $conversationUuid
     * @param string                         $from
     *
     * @return array
     *
     * @throws UnsupportedResponseException
     */
    private function translateResponse($response, $conversationUuid, $from)
    {
        foreach ($this->translateResponseServices as $translateResponseService) {
            try {
                return $translateResponseService->translate(
                    $response,
                    $conversationUuid,
                    $from
                );
            } catch (UnsupportedResponseException $e) {
                continue;
            }
        }

        throw new UnsupportedResponseException();
    }
}