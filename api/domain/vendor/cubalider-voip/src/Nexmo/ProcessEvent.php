<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\Nexmo\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessEvent
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param array  $payload
     */
    public function process($payload)
    {
        $this->manageStorage->connect()->updateOne(
            ['_id' => $payload['conversation_uuid']],
            ['$push' => [
                'events' => $payload
            ]]
        );
    }
}