<?php

namespace Cubalider\Voip;

use Cubalider\Voip\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class StartCall
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
     * @param ManageStorage         $manageStorage
     * @param ListenIncomingEvent[] $listenIncomingEventServices
     *
     * @di\arguments({
     *     listenIncomingEventServices: '#cubalider.voip.listen_incoming_event',
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        array $listenIncomingEventServices
    )
    {
        $this->manageStorage = $manageStorage;
        $this->listenIncomingEventServices = $listenIncomingEventServices;
    }

    /**
     * @param string      $provider
     * @param string      $cid
     * @param string      $from
     * @param string|null $id
     *
     * @return ConnectResponse|HangupResponse
     */
    public function start(
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

        return $response;
    }

    /**
     * @param string $from
     * @param string $id
     *
     * @return ConnectResponse|HangupResponse
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


}