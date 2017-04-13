<?php

namespace Muchacuba\Aloleiro;

use GuzzleHttp\Client;
use MongoDB\DeleteResult;
use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Request\ManageStorage as ManageRequestStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessRequests
{
    /**
     * @var ManageRequestStorage
     */
    private $manageRequestStorage;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @var string
     */
    private $sinchAppKey;

    /**
     * @var string
     */
    private $sinchAppSecret;

    /**
     * @param ManageRequestStorage $manageRequestStorage
     * @param ManageCallStorage    $manageCallStorage
     * @param string               $sinchAppKey
     * @param string               $sinchAppSecret
     *
     * @di\arguments({
     *     sinchAppKey:    "%sinch_app_key%",
     *     sinchAppSecret: "%sinch_app_secret%"
     * })
     */
    public function __construct(
        ManageRequestStorage $manageRequestStorage,
        ManageCallStorage $manageCallStorage,
        $sinchAppKey,
        $sinchAppSecret
    )
    {
        $this->manageRequestStorage = $manageRequestStorage;
        $this->manageCallStorage = $manageCallStorage;
        $this->sinchAppKey = $sinchAppKey;
        $this->sinchAppSecret = $sinchAppSecret;
    }

    /**
     * @throws \Exception
     */
    public function process()
    {
        /** @var Request[] $requests */
        $requests = $this->manageRequestStorage->connect()->find();

        foreach ($requests as $request) {
            /* Connect to sinch to get call details */

            $response = (new Client(['base_uri' => 'https://callingapi.sinch.com']))
                ->request(
                    'GET',
                    sprintf(
                        '/v1/calls/id/%s',
                        $request->getCallId()
                    ),
                    ['headers' => [
                        'Authorization' => sprintf(
                            'basic %s',
                            base64_encode(sprintf(
                                'application\%s:%s',
                                $this->sinchAppKey,
                                $this->sinchAppSecret
                            ))
                        )
                    ]]
                );

            $data = json_decode($response->getBody()->getContents(), true);

            /* Update call in out db */

            /** @var UpdateResult $result */
            $result = $this->manageCallStorage->connect()->updateOne(
                [
                    'callId' => $request->getCallId()
                ],
                ['$set' => [
                    'status' => Call::STATUS_DISCONNECTED,
                    'duration' => $data['duration'],
                    'charge' => $data['debit']['amount'],
                ]]
            );

            if ($result->getModifiedCount() == 0) {
                throw new \Exception(sprintf("Call with callId = '%s' does not exist", $request->getCallId()));
            }

            /* Delete request from queue */

            /** @var DeleteResult $result */
            $result = $this->manageRequestStorage->connect()->deleteOne([
                'callId' => $request->getCallId()
            ]);

            if ($result->getDeletedCount() == 0) {
                throw new \Exception(sprintf("Request with callId = '%s' does not exist", $request->getCallId()));
            }
        }
    }
}