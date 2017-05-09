<?php

namespace Muchacuba\Http\Aloleiro;

use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\Business\InsufficientBalanceException;
use Muchacuba\Aloleiro\Call\InvalidDataException;
use Muchacuba\Aloleiro\PrepareCall as DomainPrepareCall;
use Muchacuba\Aloleiro\CollectCalls as DomainCollectCalls;
use Symsonte\Http\Server;
use Muchacuba\Aloleiro\PickPhone as DomainPickPhone;

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
     * @var DomainPickPhone
     */
    private $pickPhone;

    /**
     * @var DomainPrepareCall
     */
    private $prepareCall;

    /**
     * @var DomainCollectCalls
     */
    private $collectCalls;
    
    /**
     * @param Server                     $server
     * @param DomainPickPhone            $pickPhone
     * @param DomainPrepareCall          $prepareCall
     * @param DomainCollectCalls $collectCalls
     */
    public function __construct(
        Server $server,
        DomainPickPhone $pickPhone,
        DomainPrepareCall $prepareCall,
        DomainCollectCalls $collectCalls
    ) {
        $this->server = $server;
        $this->pickPhone = $pickPhone;
        $this->prepareCall = $prepareCall;
        $this->collectCalls = $collectCalls;
    }

    /**
     * @http\authorization({roles: ["aloleiro_operator"]})
     * @http\resolution({method: "POST", path: "/aloleiro/prepare-call"})
     *
     * @param Business $business
     */
    public function prepare(Business $business)
    {
        $post = $this->server->resolveBody();

        $phone = $this->pickPhone->pick($business, $post['from']);

        try {
            $this->prepareCall->prepare(
                $business,
                $phone,
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
        }

        $calls = $this->collectCalls->collectPrepared($business);

        $this->server->sendResponse($calls);
    }
}
