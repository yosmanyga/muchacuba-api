<?php

namespace Muchacuba\Internauta\Mailgun;

use Muchacuba\Internauta\InsertRequest;
use Muchacuba\Internauta\InsertLog;

/**
 * @di\service()
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
     * @http\resolution({method: "POST", path: "/internauta/mailgun/push-request"})
     *
     * @param array $parsedBody
     * @param array $body
     *
     * @return string $id
     */
    public function push($parsedBody, $body)
    {
        $body = $body ?: $parsedBody;

        $id = $this->insertRequest->insert(
            $body['sender'],
            $body['recipient'],
            $body['subject'],
            $body['stripped-text']
        );

        $payload = array_merge(
            $body,
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