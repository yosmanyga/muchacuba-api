<?php

namespace Cubalider\Voip\Sinch;

use Cubalider\Voip\CompleteCall;
use Cubalider\Voip\ConnectResponse;
use Cubalider\Voip\HangupResponse;
use Cubalider\Voip\Sinch\Call\ManageStorage;
use Cubalider\Voip\StartCall;
use Cubalider\Voip\UnsupportedResponseException;
use MongoDB\Driver\Exception\BulkWriteException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessEvent
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var StartCall
     */
    private $startCall;

    /**
     * @var CompleteCall
     */
    private $completeCall;

    /**
     * @var TranslateConnectResponse
     */
    private $translateConnectResponse;

    /**
     * @var TranslateHangupResponse
     */
    private $translateHangupResponse;

    /**
     * @var TranslateContinueResponse
     */
    private $translateContinueResponse;

    /**
     * @param ManageStorage             $manageStorage
     * @param StartCall                 $startCall
     * @param CompleteCall              $completeCall
     * @param TranslateConnectResponse  $translateConnectResponse
     * @param TranslateHangupResponse   $translateHangupResponse
     * @param TranslateContinueResponse $translateContinueResponse
     */
    public function __construct(
        ManageStorage $manageStorage,
        StartCall $startCall,
        CompleteCall $completeCall,
        TranslateConnectResponse $translateConnectResponse,
        TranslateHangupResponse $translateHangupResponse,
        TranslateContinueResponse $translateContinueResponse
    )
    {
        $this->manageStorage = $manageStorage;
        $this->startCall = $startCall;
        $this->completeCall = $completeCall;
        $this->translateConnectResponse = $translateConnectResponse;
        $this->translateHangupResponse = $translateHangupResponse;
        $this->translateContinueResponse = $translateContinueResponse;
    }

    /**
     * @param array $payload
     *
     * @return array
     *
     * @throws \Exception
     */
    public function process($payload)
    {
        /* Insert call with no events */

        try {
            $this->manageStorage->connect()->insertOne(new Call(
                $payload['callid'],
                Call::STATUS_STARTED
            ));
        } catch (BulkWriteException $e) {
            if ($e->getCode() == 'E11000') {
                // Ignore it if it was already added
            } else {
                throw $e;
            }
        }

        /* Log event */

        $this->manageStorage->connect()->updateOne(
            ['_id' => $payload['callid']],
            ['$push' => [
                'events' => $payload
            ]]
        );

        /* Process event */

        switch ($payload['event']) {
            case 'ice':
                $response = $this->processICE($payload);

                break;
            case 'ace':
                $response = $this->processACE();

                break;
            case 'dice':
                $response = $this->processDICE($payload);

                break;
            default:
                throw new \Exception();
        }

        /* Log response */

        $this->manageStorage->connect()->updateOne(
            ['_id' => $payload['callid']],
            ['$push' => [
                'events' => $response
            ]]
        );

        return $response;
    }

    /**
     * @param array $payload
     *
     * @return array
     *
     * @throws UnsupportedResponseException
     */
    private function processICE($payload)
    {
        $response = $this->startCall->start(
            'sinch',
            $payload['callid'],
            $payload['cli']
        );

        if ($response instanceof ConnectResponse) {
            $response = $this->translateConnectResponse->translate(
                $response,
                $payload['to']['endpoint']
            );
        } elseif ($response instanceof HangupResponse) {
            $response = $this->translateHangupResponse->translate();
        } else {
            throw new UnsupportedResponseException();
        }

        return $response;
    }

    /**
     * @return array
     */
    private function processACE()
    {
        $response = $this->translateContinueResponse->translate();

        return $response;
    }

    /**
     * @param array $payload
     *
     * @return array
     */
    private function processDICE($payload)
    {
        $this->completeCall->complete(
            'sinch',
            $payload['callid'],
            strtotime($payload['timestamp']) - $payload['duration'],
            strtotime($payload['timestamp']),
            isset($payload['duration']) ? $payload['duration'] : null,
            $payload['debit']['amount']
        );

        $response = $this->translateHangupResponse->translate();

        return $response;
    }
}