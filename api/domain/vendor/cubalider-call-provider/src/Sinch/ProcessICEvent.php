<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\ListenIncomingCallEvent;
use Cubalider\Call\Provider\ProcessEvent;
use Cubalider\Call\Provider\Response;
use Cubalider\Call\Provider\UnsupportedEventException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.sinch.process_event',
 *          key: 'ice'
 *     }]
 * })
 */
class ProcessICEvent implements ProcessEvent
{
    /**
     * @var ListenIncomingCallEvent[]
     */
    private $listenIncomingEventServices;

    /**
     * @param ListenIncomingCallEvent[] $listenIncomingEventServices
     *
     * @di\arguments({
     *     listenIncomingEventServices: '#cubalider.call.provider.listen_incoming_call_event'
     * })
     */
    public function __construct(array $listenIncomingEventServices)
    {
        $this->listenIncomingEventServices = $listenIncomingEventServices;
    }

    /**
     * {@inheritdoc}
     */
    public function process($payload)
    {
        if ($payload['event'] != 'ice') {
            throw new UnsupportedEventException();
        }

        $response = null;

        foreach ($this->listenIncomingEventServices as $listenIncomingEventService) {
            $r = $listenIncomingEventService->listen(
                '+' . $payload['cli'],
                $payload['callid']
            );

            if ($r instanceof Response) {
                $response = $r;
            }
        }

        return $response;
    }
}