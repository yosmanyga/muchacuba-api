<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Call\Provider\ConnectResponse;
use Cubalider\Call\Provider\HangupResponse;
use Cubalider\Call\Provider\ListenIncomingCallEvent as BaseListenIncomingCallEvent;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Call\Instance;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.listen_incoming_call_event',
 *          key: 'aloleiro.business'
 *     }]
 * })
 */
class ListenIncomingCallEvent implements BaseListenIncomingCallEvent
{
    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(ManageCallStorage $manageCallStorage)
    {
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($from, $cid)
    {
        /** @var Call $call */
        $call = $this->manageCallStorage->connect()->findOne([
            'from' => $from
        ]);

        if (is_null($call)) {
            return new HangupResponse();
        }

        $this->manageCallStorage->connect()->updateOne(
            ['from' => $from],
            ['$push' => ['instances' => new Instance($cid)]]
        );

        return new ConnectResponse($call->getTo());
    }
}