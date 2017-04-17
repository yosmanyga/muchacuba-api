<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\ListenDisconnectCallEvent as BaseListenDisconnectCallEvent;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.listen_disconnect_call_event',
 *          key: 'cubalider.call.provider.sinch'
 *     }]
 * })
 */
class ListenDisconnectCallEvent implements BaseListenDisconnectCallEvent
{
    /**
     * @var EnqueueRequest
     */
    private $enqueueRequest;

    /**
     * @param EnqueueRequest $enqueueRequest
     */
    public function __construct(EnqueueRequest $enqueueRequest)
    {
        $this->enqueueRequest = $enqueueRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($cid)
    {
        $this->enqueueRequest->enqueue($cid);
    }
}