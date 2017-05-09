<?php

namespace Muchacuba\Http\Internauta\Mailgun;

use Symsonte\Http\Server;
use Muchacuba\Internauta\Mailgun\PushRequest as DomainPushRequest;

/**
 * @di\controller({deductible: true})
 */
class PushRequest
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPushRequest
     */
    private $pushRequest;

    /**
     * @param Server          $server
     * @param DomainPushRequest $pushRequest
     */
    public function __construct(
        Server $server, 
        DomainPushRequest $pushRequest
    )
    {
        $this->server = $server;
        $this->pushRequest = $pushRequest;
    }

    /**
     * @http\resolution({method: "POST", path: "/internauta/mailgun/push-request"})
     */
    public function push()
    {
        $post = $this->server->resolveParsedBody();

        $sender = $post['sender'];
        unset($post['sender']);
            
        $recipient = $post['recipient'];
        unset($post['recipient']);
        
        $subject = $post['subject'];
        unset($post['subject']);
        
        if (isset($post['stripped-text'])) {
            $body = $post['stripped-text'];
            unset($post['stripped-text']);
        } else {
            $body = $post['body-plain'];
            unset($post['body-plain']);
        }

        $extra = $post;

        $this->pushRequest->push(
            $sender,
            $recipient,
            $subject,
            $body,
            $extra
        );

        $this->server->sendResponse();
    }
}
