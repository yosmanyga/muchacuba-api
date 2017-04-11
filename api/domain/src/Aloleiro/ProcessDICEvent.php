<?php

namespace Muchacuba\Aloleiro;

use GuzzleHttp\Client;
use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessDICEvent
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var string
     */
    private $sinchAppKey;

    /**
     * @var string
     */
    private $sinchAppSecret;

    /**
     * @param ManageStorage $manageStorage
     * @param string        $sinchAppKey
     * @param string        $sinchAppSecret
     *
     * @di\arguments({
     *     sinchAppKey:    "%sinch_app_key%",
     *     sinchAppSecret: "%sinch_app_secret%"
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        $sinchAppKey,
        $sinchAppSecret
    )
    {
        $this->manageStorage = $manageStorage;
        $this->sinchAppKey = $sinchAppKey;
        $this->sinchAppSecret = $sinchAppSecret;
    }

    /**
     * @param string $callId
     *
     * @return array
     *
     * @throws \Exception
     */
    public function process($callId)
    {
        /** @var Call $call */
        $call = $this->manageStorage->connect()->findOne([
            'callId' => $callId,
            'status' => Call::STATUS_ANSWERED
        ]);

        if (is_null($call)) {
            throw new \Exception(sprintf("Call with callId = '%s' does not exist", $callId));
        }

        $response = (new Client(['base_uri' => 'https://callingapi.sinch.com']))
            ->request(
                'GET',
                sprintf(
                    '/v1/calls/id/%s',
                    $callId
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

        /** @var UpdateResult $result */
        $result = $this->manageStorage->connect()->updateOne(
            [
                '_id' => $call->getId()
            ],
            ['$set' => [
                'status' => Call::STATUS_DISCONNECTED,
                'duration' => $data['duration'],
                'charge' => $data['debit']['amount'],
            ]]
        );

        if ($result->getModifiedCount() == 0) {
            throw new \Exception(sprintf("Call with callId = '%s' does not exist", $callId));
        }

        return null;
    }
}