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
     *
     * @return string $id
     */
    public function push($parsedBody)
    {
        return $this->process($parsedBody);
    }

    /**
     * @http\resolution({method: "POST", path: "/internauta/debug"})
     *
     * @param array $body
     *
     * @return string $id
     */
    public function debug($body)
    {
        return $this->process($body);
    }

    private function process($body)
    {
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