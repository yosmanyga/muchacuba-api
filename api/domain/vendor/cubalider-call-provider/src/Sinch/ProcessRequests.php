<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\ListenSummaryCallEvent;
use GuzzleHttp\Client;
use MongoDB\DeleteResult;
use Cubalider\Call\Provider\Sinch\Request\ManageStorage as ManageRequestStorage;

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
     * @var ListenSummaryCallEvent[]
     */
    private $listenSummaryEventServices;

    /**
     * @var string
     */
    private $sinchAppKey;

    /**
     * @var string
     */
    private $sinchAppSecret;

    /**
     * @param ManageRequestStorage     $manageRequestStorage
     * @param ListenSummaryCallEvent[] $listenSummaryEventServices
     * @param string                   $sinchAppKey
     * @param string                   $sinchAppSecret
     *
     * @di\arguments({
     *     listenAnswerEventServices: "#cubalider.call.provider.listen_summary_call_event",
     *     sinchAppKey:               "%sinch_app_key%",
     *     sinchAppSecret:            "%sinch_app_secret%"
     * })
     */
    public function __construct(
        ManageRequestStorage $manageRequestStorage,
        array $listenSummaryEventServices,
        $sinchAppKey,
        $sinchAppSecret
    )
    {
        $this->manageRequestStorage = $manageRequestStorage;
        $this->listenSummaryEventServices = $listenSummaryEventServices;
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

            /* Call listeners */

            foreach ($this->listenSummaryEventServices as $listenSummaryEventService) {
                $listenSummaryEventService->listen(
                    $data['callid'],
                    $data['duration'],
                    $data['debit']['amount']
                );
            }

            /* Delete request from queue */

            /** @var DeleteResult $result */
            $result = $this->manageRequestStorage->connect()->deleteOne([
                '_id' => $request->getCallId()
            ]);

            if ($result->getDeletedCount() == 0) {
                throw new \Exception(sprintf("Request with callId = '%s' does not exist", $request->getCallId()));
            }
        }
    }
}