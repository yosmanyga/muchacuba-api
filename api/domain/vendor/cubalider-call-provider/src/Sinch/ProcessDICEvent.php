<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\ListenDisconnectCallEvent;
use Cubalider\Call\Provider\ProcessEvent;
use Cubalider\Call\Provider\Response;
use Cubalider\Call\Provider\UnsupportedEventException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.sinch.process_event',
 *          key: 'dice'
 *     }]
 * })
 */
class ProcessDICEvent implements ProcessEvent
{
    /**
     * @var ListenDisconnectCallEvent[]
     */
    private $listenDisconnectEventServices;

    /**
     * @param ListenDisconnectCallEvent[] $listenDisconnectEventServices
     *
     * @di\arguments({
     *     listenDisconnectEventServices: '#cubalider.call.provider.listen_disconnect_call_event'
     * })
     */
    public function __construct(array $listenDisconnectEventServices)
    {
        $this->listenDisconnectEventServices = $listenDisconnectEventServices;
    }

    /**
     * {@inheritdoc}
     */
    public function process($payload)
    {
        if ($payload['event'] != 'dice') {
            throw new UnsupportedEventException();
        }

        $response = null;

        foreach ($this->listenDisconnectEventServices as $listenDisconnectEventService) {
            $r = $listenDisconnectEventService->listen(
                $payload['callid'],
                strtotime($payload['timestamp']),
                $payload['duration'],
                $payload['debit']['amount']
            );

            if ($r instanceof Response) {
                $response = $r;
            }
        }

        return $response;
    }
}