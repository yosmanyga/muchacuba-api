<?php

namespace Muchacuba\Http\Topup;

use Muchacuba\Topup\Recharge\InvalidAccountException;
use Muchacuba\Topup\ResolveProviders as DomainResolveProviders;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ResolveProviders
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainResolveProviders
     */
    private $resolveProviders;

    /**
     * @param Server                 $server
     * @param DomainResolveProviders $resolveProviders
     */
    public function __construct(
        Server $server,
        DomainResolveProviders $resolveProviders
    ) {
        $this->server = $server;
        $this->resolveProviders = $resolveProviders;
    }

    /**
     * @http\resolution({method: "GET", path: "/topup/resolve-providers/{country}/{prefix}/{account}"})
     *
     * @param string $country
     * @param string $prefix
     * @param string $account
     */
    public function resolve($country, $prefix, $account)
    {
        try {
            $providers = $this->resolveProviders->resolve($country, $prefix, $account);
        } catch (InvalidAccountException $e) {
            $this->server->sendResponse([
                'type' => 'invalid-field',
                'payload' => [
                    'field' => 'account',
                ]
            ], 422);

            return;
        }

        $this->server->sendResponse($providers);
    }
}
