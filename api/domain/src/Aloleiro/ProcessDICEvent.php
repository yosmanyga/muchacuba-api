<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessDICEvent
{
    /**
     * @var EnqueueRequest
     */
    private $enqueueRequest;

    /**
     * @param EnqueueRequest $enqueueRequest
     */
    public function __construct(
        EnqueueRequest $enqueueRequest
    )
    {
        $this->enqueueRequest = $enqueueRequest;
    }

    /**
     * @param string $callId
     */
    public function process($callId)
    {
        $this->enqueueRequest->enqueue($callId);
    }
}