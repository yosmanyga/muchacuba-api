<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessICEvent
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(ManageStorage $manageStorage)
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $from
     * @param string $callId
     * @param string $bridge
     *
     * @return array
     */
    public function process($from, $callId, $bridge)
    {
        /** @var Call $call */
        $call = $this->manageStorage->connect()->findOne([
            'from' => $from,
            'status' => Call::STATUS_PREPARED
        ]);

        if (is_null($call)) {
            return $this->prepareHangup();
        }

        return $this->prepareConnect($call, $callId, $bridge);
    }

    /**
     * @return array
     */
    private function prepareHangup()
    {
        return [
            'action' => [
                'name' => 'Hangup'
            ]
        ];
    }

    /**
     * @param Call   $call
     * @param string $callId
     * @param string $bridge
     *
     * @return array
     */
    private function prepareConnect(Call $call, $callId, $bridge)
    {
        $this->manageStorage->connect()->updateOne(
            [
                '_id' => $call->getId()
            ],
            ['$set' => [
                'callId' => $callId,
                'status' => Call::STATUS_FORWARDING
            ]]
        );

        return [
            'action' => [
                'name' => 'ConnectPSTN',
                'number' => $call->getTo(),
                'maxDuration' => 3600,
                'cli' => $bridge //$call->getFrom()
            ]
        ];
    }
}