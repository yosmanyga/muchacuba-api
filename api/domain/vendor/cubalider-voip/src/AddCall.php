<?php

namespace Cubalider\Voip;

use Cubalider\Voip\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class AddCall
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var ListenIncomingEvent[]
     */
    private $listenIncomingEventServices;

    /**
     * @var TranslateResponse[]
     */
    private $translateResponseServices;

    /**
     * @param ManageStorage         $manageStorage
     * @param ListenIncomingEvent[] $listenIncomingEventServices
     * @param TranslateResponse[]   $translateResponseServices
     *
     * @di\arguments({
     *     listenIncomingEventServices: '#cubalider.voip.listen_incoming_event',
     *     translateResponseServices:   '#cubalider.voip.nexmo.translate_response'
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        array $listenIncomingEventServices,
        array $translateResponseServices
    )
    {
        $this->manageStorage = $manageStorage;
        $this->listenIncomingEventServices = $listenIncomingEventServices;
        $this->translateResponseServices = $translateResponseServices;
    }

    /**
     * @param string      $provider
     * @param string      $cid
     * @param string      $from
     * @param string|null $id
     *
     * @return string
     */
    public function add(
        $provider,
        $cid,
        $from,
        $id = null
    )
    {
        $id = $id ?: uniqid();

        $this->manageStorage->connect()->insertOne(new Call(
            $id,
            $provider,
            $cid
        ));

        $response = $this->callListeners($from, $id);

        return $this->translateResponse($response, $cid, $from);
    }

    /**
     * @param string $from
     * @param string $id
     *
     * @return ConnectResponse|null
     */
    private function callListeners($from, $id)
    {
        $finalResponse = null;
        foreach ($this->listenIncomingEventServices as $listenIncomingEventService) {
            $response = $listenIncomingEventService->listen(
                $from,
                $id
            );

            if (
                $response instanceof ConnectResponse
                || $response instanceof HangupResponse
            ) {
                $finalResponse = $response;
            }
        }

        return $finalResponse;
    }

    /**
     * @param ConnectResponse|HangupResponse $response
     * @param string                         $cid
     * @param string                         $from
     *
     * @return string
     *
     * @throws UnsupportedResponseException
     */
    private function translateResponse($response, $cid, $from)
    {
        foreach ($this->translateResponseServices as $translateResponseService) {
            try {
                return $translateResponseService->translate($response, $cid, $from);
            } catch (UnsupportedResponseException $e) {
                continue;
            }
        }

        throw new UnsupportedResponseException();
    }
}