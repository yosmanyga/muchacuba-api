<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\ListenAnswerCallEvent;
use Cubalider\Call\Provider\ProcessEvent;
use Cubalider\Call\Provider\Response;
use Cubalider\Call\Provider\UnsupportedEventException;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.sinch.process_event',
 *          key: 'ace'
 *     }]
 * })
 */
class ProcessACEvent implements ProcessEvent
{
    /**
     * @var ListenAnswerCallEvent[]
     */
    private $listenAnswerEventServices;

    /**
     * @param ListenAnswerCallEvent[] $listenAnswerEventServices
     *
     * @di\arguments({
     *     listenAnswerEventServices: '#cubalider.call.provider.listen_answer_call_event'
     * })
     */
    public function __construct(array $listenAnswerEventServices)
    {
        $this->listenAnswerEventServices = $listenAnswerEventServices;
    }

    /**
     * {@inheritdoc}
     */
    public function process($payload)
    {
        if ($payload['event'] != 'ace') {
            throw new UnsupportedEventException();
        }

        $response = null;

        foreach ($this->listenAnswerEventServices as $listenAnswerEventService) {
            $r = $listenAnswerEventService->listen(
                $payload['callid']
            );

            if ($r instanceof Response) {
                $response = $r;
            }
        }

        return $response;
    }
}