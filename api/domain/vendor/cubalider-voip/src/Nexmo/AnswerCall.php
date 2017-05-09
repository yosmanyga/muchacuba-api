<?php

namespace Cubalider\Voip\Nexmo;

use Cubalider\Voip\Nexmo\Call\ManageStorage;
use Cubalider\Voip\AddCall as BaseAddCall;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AnswerCall
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var BaseAddCall
     */
    private $addCall;

    /**
     * @param ManageStorage $manageStorage
     * @param BaseAddCall   $addCall
     */
    public function __construct(
        ManageStorage $manageStorage,
        BaseAddCall $addCall
    )
    {
        $this->manageStorage = $manageStorage;
        $this->addCall = $addCall;
    }

    /**
     * @param array $payload
     *
     * @return string
     */
    public function answer($payload)
    {
        $this->manageStorage->connect()->insertOne(new Call(
            $payload['conversation_uuid'],
            $payload
        ));

        return $this->addCall->add(
            'nexmo',
            $payload['conversation_uuid'],
            $payload['from']
        );
    }
}