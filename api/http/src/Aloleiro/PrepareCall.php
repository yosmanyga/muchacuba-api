<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business\InsufficientBalanceException;
use Muchacuba\Aloleiro\Call\InvalidDataException;
use Muchacuba\Aloleiro\NonExistentPhoneException;
use Muchacuba\Aloleiro\PrepareCall as DomainPrepareCall;
use Muchacuba\Aloleiro\CollectPreparedCalls as DomainCollectPreparedCalls;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class PrepareCall
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPrepareCall
     */
    private $prepareCall;

    /**
     * @var DomainCollectPreparedCalls
     */
    private $collectPreparedCalls;
    
    /**
     * @param Server                     $server
     * @param DomainPrepareCall          $prepareCall
     * @param DomainCollectPreparedCalls $collectPreparedCalls
     */
    public function __construct(
        Server $server,
        DomainPrepareCall $prepareCall,
        DomainCollectPreparedCalls $collectPreparedCalls
    ) {
        $this->server = $server;
        $this->prepareCall = $prepareCall;
        $this->collectPreparedCalls = $collectPreparedCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "POST", uri: "/aloleiro/prepare-call"})
     *
     * @param string $uniqueness
     */
    public function search($uniqueness)
    {
        $post = $this->server->resolveBody();

        try {
            $this->prepareCall->prepare(
                $uniqueness,
                $post['from'],
                $post['to']
            );
        } catch (InvalidDataException $e) {
            $this->server->sendResponse([
                'type' => 'invalid-field',
                'payload' => [
                    'field' => $e->getField(),
                ]
            ], 422);

            return;
        } catch (InsufficientBalanceException $e) {
            $this->server->sendResponse([
                'type' => 'insufficient-balance'
            ], 403);

            return;
        } catch (NonExistentPhoneException $e) {
            $this->server->sendResponse([
                'type' => 'non-existent-phone',
            ], 422);

            return;
        }

        $calls = $this->collectPreparedCalls->collect($uniqueness);

        $this->server->sendResponse($calls);
    }
}
