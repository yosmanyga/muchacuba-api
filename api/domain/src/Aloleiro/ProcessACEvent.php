<?php

namespace Muchacuba\Aloleiro;

use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessACEvent
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
     *
     * @return array
     *
     * @throws \Exception
     */
    public function process($callId)
    {
        /** @var UpdateResult $result */
        $result = $this->manageStorage->connect()->updateOne(
            [
                'callId' => $callId
            ],
            ['$set' => [
                'status' => Call::STATUS_ANSWERED
            ]]
        );

        if ($result->getModifiedCount() == 0) {
            throw new \Exception(sprintf("Call with callId = '%s' does not exist", $callId));
        }

        return [
            'action' => [
                'name' => 'Continue'
            ]
        ];
    }
}