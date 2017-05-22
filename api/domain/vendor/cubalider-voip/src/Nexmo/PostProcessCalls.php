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
            $inbound = [];
            $outbound = [];

            foreach ($call->getEvents() as $event) {
                if (isset($event['status'])) {
                    if (
                        $event['status'] == 'completed'
                        || $event['status'] == 'failed'
                    ) {
                        // Less than 10 seconds?
                        if (
                            isset($event['end_time'])
                            && strtotime($event['end_time']) + 10 > time()
                        ) {
                            // Ignore this call, let's wait more than 10 seconds,
                            // to make sure that nexmo sends all events
                            break;
                        }

                        if ($event['direction'] == 'inbound') {
                            $inbound['price'] = isset($event['price'])
                                ? $event['price'] : null;

                            $inbound['duration'] = isset($event['duration'])
                                ? $event['duration'] : null;

                            $inbound['start_time'] = isset($event['start_time'])
                                ? $event['start_time'] : null;

                            $inbound['end_time'] = isset($event['end_time'])
                                ? $event['end_time'] : null;
                        } else {
                            $outbound['price'] = isset($event['price'])
                                ? $event['price'] : null;

                            $outbound['duration'] = isset($event['duration'])
                                ? $event['duration'] : null;

                            $outbound['start_time'] = isset($event['start_time'])
                                ? $event['start_time'] : null;

                            $outbound['end_time'] = isset($event['end_time'])
                                ? $event['end_time'] : null;
                        }
                    }
                }
            }

            $start = null;
            $end = null;
            $duration = 0;
            $cost = 0;

            if (!empty($outbound)) {
                $start = !is_null($outbound['start_time'])
                    ? strtotime($outbound['start_time']) : null;
                $end = !is_null($outbound['end_time'])
                    ? strtotime($outbound['end_time']) : null;
                $duration = !is_null($outbound['duration'])
                    ? (int) $outbound['duration'] : null;
                $cost = !is_null($outbound['price'])
                    ? (float) $outbound['price'] : null;
            }

            if (!empty($inbound)) {
                /* Start time, end time and duration from inbound are more
                   important than the outbound values. So let's override them */

                $start = !is_null($inbound['start_time'])
                    ? strtotime($inbound['start_time']) : null;
                $end = !is_null($inbound['end_time'])
                    ? strtotime($inbound['end_time']) : null;
                $duration = !is_null($inbound['duration'])
                    ? (int) $inbound['duration'] : null;

                /* Cost is added to the outbound */

                $cost += $cost + !is_null($inbound['price'])
                    ? (float) $inbound['price'] : 0;
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