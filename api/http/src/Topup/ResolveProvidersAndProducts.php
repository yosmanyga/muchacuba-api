<?php

namespace Muchacuba\Http\Topup;

use Muchacuba\Topup\ResolveProviders as DomainResolveProviders;
use Muchacuba\Topup\ResolveProducts as DomainResolveProducts;
use Symsonte\Http\Server;

/**
 * @di\controller({deductible: true})
 */
class ResolveProvidersAndProducts
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
     * @var DomainResolveProducts
     */
    private $resolveProducts;

    /**
     * @param Server                 $server
     * @param DomainResolveProviders $resolveProviders
     * @param DomainResolveProducts  $resolveProducts
     */
    public function __construct(
        Server $server,
        DomainResolveProviders $resolveProviders,
        DomainResolveProducts $resolveProducts
    ) {
        $this->server = $server;
        $this->resolveProviders = $resolveProviders;
        $this->resolveProducts = $resolveProducts;
    }

    /**
     * @http\resolution({method: "POST", path: "/topup/resolve-providers-and-products"})
     */
    public function resolve()
    {
        $post = $this->server->resolveBody();

        $providers = $this->resolveProviders->resolve(
            $post['account']
        );

        $products = [];
        foreach ($providers as $provider) {
            $products = array_merge(
                $products,
                $this->resolveProducts->resolve($provider)
            );
        }

        $this->server->sendResponse([
            'providers' => $providers,
            'products' => $products
        ]);
    }
}
