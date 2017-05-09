<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\Nexmo\Call\LogEvent;
use Cubalider\Voip\ReceiveEvent;
use Cubalider\Voip\UnsupportedEventException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessEvent
{
    /**
     * @var LogEvent
     */
    private $logEvent;

    /**
     * @var ReceiveEvent[]
     */
    private $receiveEventServices;

    /**
     * @param LogEvent       $logEvent
     * @param ReceiveEvent[] $receiveEventServices
     *
     * @di\arguments({
     *     receiveEventServices: '#cubalider.voip.nexmo.receive_event'
     * })
     */
    public function __construct(
        LogEvent $logEvent,
        array $receiveEventServices
    )
    {
        $this->logEvent = $logEvent;
        $this->receiveEventServices = $receiveEventServices;
    }

    /**
     * @param array $payload
     */
    public function process($payload)
    {
        $this->logEvent->log(
            $payload['conversation_uuid'],
            $payload
        );

        try {
            $this->receiveEvent($payload);
        } catch (UnsupportedEventException $e) {
        }
    }

    /**
     * @param array $payload
     *
     * @return mixed
     *
     * @throws UnsupportedEventException
     */
    private function receiveEvent($payload)
    {
        foreach ($this->receiveEventServices as $receiveEventService) {
            try {
                return $receiveEventService->receive($payload);
            } catch (UnsupportedEventException $e) {
                continue;
            }
        }

        throw new UnsupportedEventException();
    }
}