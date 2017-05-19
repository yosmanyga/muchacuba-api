<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\CompleteCall;
use Cubalider\Voip\Nexmo\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PostProcessCalls
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var CompleteCall
     */
    private $completeCall;

    /**
     * @param ManageStorage $manageStorage
     * @param CompleteCall  $completeCall
     */
    public function __construct(
        ManageStorage $manageStorage,
        CompleteCall $completeCall
    )
    {
        $this->manageStorage = $manageStorage;
        $this->completeCall = $completeCall;
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess()
    {
        /** @var Call[] $calls */
        $calls = $this->manageStorage->connect()->find([
            'status' => Call::STATUS_STARTED
        ]);

        foreach ($calls as $call) {
            $completed = false;
            $inbound = [];
            $outbound = [];

            foreach ($call->getEvents() as $event) {
                if (
                    isset($event['status'])
                    && $event['status'] == 'completed'
                ) {
                    // Less than 30 seconds?
                    if (strtotime($event['end_time']) + 30 > time()) {
                        // Ignore this call, let's wait more than 30 seconds,
                        // to make sure that nexmo sends all events
                        break;
                    }

                    $completed = true;

                    if ($event['direction'] == 'inbound') {
                        $inbound = [
                            'price' => $event['price'],
                            'duration' => $event['duration'],
                            'start_time' => $event['start_time'],
                            'end_time' => $event['end_time']
                        ];
                    } else {
                        $outbound = [
                            'price' => $event['price'],
                            'duration' => $event['duration'],
                            'start_time' => $event['start_time'],
                            'end_time' => $event['end_time']
                        ];
                    }
                }
            }

            if (!$completed) {
                // TODO
                // Call ended more than 30 seconds ago, doesn't have completed event
                continue;
            }

            $start = null;
            $end = null;
            $duration = 0;
            $cost = 0;

            if (!empty($outbound)) {
                $start = strtotime($outbound['start_time']);
                $end = strtotime($outbound['end_time']);
                $duration = (int) $outbound['duration'];
                $cost = (float) $outbound['price'];
            }

            if (!empty($inbound)) {
                /* Start time, end time and duration from inbound are more
                   important than the outbound values. So let's override them */

                $start = strtotime($inbound['start_time']);
                $end = strtotime($inbound['end_time']);
                $duration = (int) $inbound['duration'];

                /* Cost is added to the outbound */

                $cost += (float) $inbound['price'];
            }

            $this->manageStorage->connect()->updateOne(
                [
                    '_id' => $call->getId()
                ],
                [
                    '$set' => [
                        'status' => Call::STATUS_COMPLETED
                    ]
                ]
            );

            $this->completeCall->complete(
                'nexmo',
                $call->getId(),
                $start,
                $end,
                $duration,
                $cost,
                'EUR'
            );
        }
    }
}