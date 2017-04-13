<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Request\ManageStorage as ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class EnqueueRequest
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
     * @param string $callId
     */
    public function enqueue($callId)
    {
        $this->manageStorage->connect()->insertOne(new Request(
            $callId
        ));
    }
}