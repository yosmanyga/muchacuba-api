<?php

namespace Cubalider\Voip\Nexmo\Call;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class LogEvent
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
     * @param string $id
     * @param array  $payload
     */
    public function log($id, $payload)
    {
        $this->manageStorage->connect()->updateOne(
            ['_id' => $id],
            ['$push' => [
                'events' => $payload
            ]]
        );
    }
}