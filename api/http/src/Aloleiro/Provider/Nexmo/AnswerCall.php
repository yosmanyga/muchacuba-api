<?php

namespace Muchacuba\Http\Aloleiro\Provider\Nexmo;

use Cubalider\Voip\Nexmo\AnswerCall as DomainAnswerCall;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class AnswerCall
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainAnswerCall
     */
    private $answerCall;

    /**
     * @param Server            $server
     * @param DomainAnswerCall  $answerCall
     */
    public function __construct(
        Server $server,
        DomainAnswerCall $answerCall
    ) {
        $this->server = $server;
        $this->answerCall = $answerCall;
    }

    /**
     * @http\resolution({method: "GET", path: "/aloleiro/provider/nexmo/answer-call"})
     */
    public function answer()
    {
        /** @var array $query */
        $query = $this->server->resolveQuery();

        $result = $this->answerCall->answer($query);

        $this->server->sendResponse($result);
    }
}
