<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\Call;
use Cubalider\Voip\ListenCompletedEvent;
use Cubalider\Voip\PickCall;
use Cubalider\Voip\ReceiveEvent;
use Cubalider\Voip\UnsupportedEventException;
use Cubalider\Voip\UpdateCall;

/**
 * @di\service({
 *     deductible: true,
 *     tags: ['cubalider.voip.nexmo.receive_event']
 * })
 */
class ReceiveCompletedEvent implements ReceiveEvent
{
    /**
     * @var PickCall
     */
    private $pickCall;

    /**
     * @var UpdateCall
     */
    private $updateCall;

    /**
     * @var ListenCompletedEvent[]
     */
    private $listenCompletedEventServices;

    /**
     * @param PickCall               $pickCall
     * @param UpdateCall             $updateCall
     * @param ListenCompletedEvent[] $listenCompletedEventServices
     *
     * @di\arguments({
     *     listenCompletedEventServices: '#cubalider.voip.listen_completed_event'
     * })
     */
    public function __construct(
        PickCall $pickCall,
        UpdateCall $updateCall,
        array $listenCompletedEventServices
    )
    {
        $this->pickCall = $pickCall;
        $this->updateCall = $updateCall;
        $this->listenCompletedEventServices = $listenCompletedEventServices;
    }

    /**
     * {@inheritdoc}
     */
    public function receive($payload)
    {
        if (!isset($payload['status'])
            || !in_array(
                $payload['status'],
                [
                    'machine',
                    'completed',
                    'timeout',
                    'failed',
                    'rejected',
                    'unanswered',
                    'busy'
                ]
            )
        ) {
            throw new UnsupportedEventException();
        }

        if (!isset($payload['direction'])
            || !in_array(
                $payload['direction'],
                [
                    'inbound',
                    'outbound'
                ]
            )
        ) {
            throw new UnsupportedEventException();
        }

        // Find the internal call

        $call = $this->pickCall->pick(
            'nexmo',
            $payload['conversation_uuid']
        );

        // Update call according the direction

        $call = $payload['direction'] == 'inbound'
            ? $this->updateCall->updateWithInbound(
                $call->getId(),
                (float) $payload['price'],
                strtotime($payload['start_time']),
                strtotime($payload['end_time']),
                (int) $payload['duration']
            )
            : $this->updateCall->updateWithOutbound(
                $call->getId(),
                (float) $payload['price']
            );

        // Call listeners after both events are processed

        if ($call->getStatus() == Call::STATUS_SECOND) {
            $this->callListeners($call);
        }
    }

    /**
     * @param Call $call
     */
    private function callListeners(Call $call)
    {
        foreach ($this->listenCompletedEventServices as $listenCompletedEventService) {
            $listenCompletedEventService->listen(
                $call->getId(),
                $call->getStart(),
                $call->getEnd(),
                $call->getDuration(),
                $call->getCost()
            );
        }
    }
}