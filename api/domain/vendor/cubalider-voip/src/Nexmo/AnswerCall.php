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
     * @return array
     */
    public function answer($payload)
    {
        // Insert call as it comes

        $this->manageStorage->connect()->insertOne(new Call(
            $payload['conversation_uuid'],
            $payload
        ));

        /* Nexmo doesn't add country prefix to calls from Venezuela */
        // TODO: https://github.com/giggsey/libphonenumber-for-php

        if (
            // Call inside Venezuela?
            $payload['to'] == '582123353020'
            // Not having country code?
            && strpos($payload['from'], '+58') === false
        ) {
            // Add country code
            $payload['from'] = sprintf('+58%s', $payload['from']);
        }

        $response = $this->addCall->add(
            'nexmo',
            $payload['conversation_uuid'],
            $payload['from']
        );

        $this->manageStorage->connect()->updateOne(
            [
                '_id' => $payload['conversation_uuid']
            ],
            [
                '$set' => ['answerResponse' => $response]
            ]
        );

        return $response;
    }
}