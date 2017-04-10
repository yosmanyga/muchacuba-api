<?php

namespace Muchacuba\Internauta\Mailgun;

use Muchacuba\Internauta\InsertRequest;
use Muchacuba\Internauta\InsertLog;

/**
 * @di\service({
 *   deductible: true
 * })
 */
class PushRequest
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
     * @param string $sender
     * @param string $receptor
     * @param string $subject
     * @param string $body
     * @param array  $extra
     *
     * @return string $id
     */
    public function push($sender, $receptor, $subject, $body, $extra)
    {
        $id = $this->insertRequest->insert(
            $sender,
            $receptor,
            $subject,
            $body
        );

        $payload = [
            'id' => $id,
            'sender' => $sender,
            'receptor' => $receptor,
            'subject' => $subject,
            'body' => $body
        ];

        if (!empty($extra)) {
            $payload['extra'] = $extra;
        }

        $this->insertLog->insert(
            sprintf('%s', get_class($this)),
            $payload,
            time()
        );

        return $id;
    }
}