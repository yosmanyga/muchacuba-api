<?php

namespace Muchacuba\Cli\Topup;

use Muchacuba\Topup\ResolveProviders as DomainResolveProviders;
use Muchacuba\Topup\ResolveProducts as DomainResolveProducts;

/**
 * @di\command({deductible: true})
 */
class ResolveProducts
{
    /**
     * @var DomainResolveProviders
     */
    private $resolveProviders;

    /**
     * @var DomainResolveProducts
     */
    private $resolveProducts;

    /**
     * @param DomainResolveProviders $resolveProviders
     * @param DomainResolveProducts  $resolveProducts
     */
    public function __construct(
        DomainResolveProviders $resolveProviders,
        DomainResolveProducts $resolveProducts
    )
    {
        $this->resolveProviders = $resolveProviders;
        $this->resolveProducts = $resolveProducts;
    }

    /**
     * @cli\resolution({command: "topup.resolve_products"})
     */
    public function resolve()
    {
        $providers = $this->resolveProviders->resolve('05353377172');

        $products = [];
        foreach ($providers as $provider) {
            $products[] = $this->resolveProducts->resolve($provider);
        }
    }
}
