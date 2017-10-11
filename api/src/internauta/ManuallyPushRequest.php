<?php

namespace Muchacuba\Internauta;

use Muchacuba\Internauta\InsertRequest;
use Muchacuba\Internauta\InsertLog;

/**
 * @di\service()
 */
class ManuallyPushRequest
{
    /**
     * @var InsertRequest
     */
    private $insertRequest;

    /**
     * @var InsertLog
     */
    private $insertLog;

    /**
     * @param InsertRequest $insertRequest
     * @param InsertLog    $insertLog
     */
    public function __construct(
        InsertRequest $insertRequest,
        InsertLog $insertLog
    )
    {
        $this->insertRequest = $insertRequest;
        $this->insertLog = $insertLog;
    }

    /**
     * @cli\resolution({command: "/internauta/manually-push-request"})
     *
     * @param string $sender
     * @param string $recipient
     * @param string $subject
     *
     * @return string
     */
    public function push($sender, $recipient, $subject)
    {
        $parsedBody = [
            'sender' => $sender,
            'recipient' => $recipient,
            'subject' => $subject,
            'stripped-text' => '',
        ];

        $id = $this->insertRequest->insert(
            $parsedBody['sender'],
            $parsedBody['recipient'],
            $parsedBody['subject'],
            $parsedBody['stripped-text']
        );

        $payload = array_merge(
            $parsedBody,
            [
                'id' => $id,
            ]
        );

        $this->insertLog->insert(
            sprintf('%s', get_class($this)),
            $payload,
            time()
        );

        return $id;
    }
}