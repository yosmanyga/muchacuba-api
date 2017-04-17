<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Call\Provider\NullResponse;
use Cubalider\Call\Provider\ListenDisconnectCallEvent as BaseListenDisconnectCallEvent;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.listen_disconnect_call_event',
 *          key: 'aloleiro.business'
 *     }]
 * })
 */
class ListenDisconnectCallEvent implements BaseListenDisconnectCallEvent
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
    public function listen($cid)
    {
        return new NullResponse();
    }
}