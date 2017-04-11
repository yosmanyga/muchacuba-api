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
     * @param ManageStorage $manageStorage
     */
    public function __construct(ManageStorage $manageStorage)
    {
        $this->manageStorage = $manageStorage;
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
                            'ba0f59a1-6c76-43bb-9727-b7b9d14424ab',
                            'YvuKqKsj3UCd72MykFfS9A=='
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