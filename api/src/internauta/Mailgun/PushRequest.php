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
     * @param array $body
     * @param array $parsedBody
     *
     * @return string $id
     */
    public function push($body, $parsedBody)
    {
        if (!$parsedBody) {
            $parsedBody = $body;
        }

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