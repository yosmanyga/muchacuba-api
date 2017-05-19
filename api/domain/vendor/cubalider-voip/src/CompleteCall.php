<?php

namespace Cubalider\Voip;

use Cubalider\Voip\Call\ManageStorage;
use Muchacuba\Aloleiro\NonExistentCallException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CompleteCall
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var ListenCompletedEvent[]
     */
    private $listenCompletedEventServices;

    /**
     * @param ManageStorage          $manageStorage
     * @param ListenCompletedEvent[] $listenCompletedEventServices
     *
     * @di\arguments({
     *     listenCompletedEventServices: '#cubalider.voip.listen_completed_event'
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        array $listenCompletedEventServices
    )
    {
        $this->manageStorage = $manageStorage;
        $this->listenCompletedEventServices = $listenCompletedEventServices;
    }

    /**
     * @param string      $provider
     * @param string      $cid
     * @param int         $start
     * @param int         $end
     * @param int         $duration
     * @param float       $cost
     * @param string|null $currency
     *
     * @throws NonExistentCallException
     */
    public function complete($provider, $cid, $start, $end, $duration, $cost, $currency = 'USD')
    {
        $call = $this->manageStorage->connect()->findOne([
            'cid' => $cid,
            'provider' => $provider
        ]);

        if (is_null($call)) {
            throw new NonExistentCallException();
        }

        $this->manageStorage->connect()->updateOne(
            ['_id' => $call->getId()],
            [
                '$set' => [
                    'duration' => $duration,
                    'start' => $start,
                    'end' => $end,
                    'cost' => $cost,
                    'currency' => $currency,
                ]
            ]
        );

        foreach ($this->listenCompletedEventServices as $listenCompletedEventService) {
            $listenCompletedEventService->listen(
                $call->getId(),
                $start,
                $end,
                $duration,
                $cost,
                $currency
            );
        }
    }
}
