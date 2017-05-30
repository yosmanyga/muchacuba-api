<?php

namespace Muchacuba\Topup;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ResolveProducts
{
    /**
     * @var ResolveProviders
     */
    private $resolveProviders;
    
    /**
     * @var CollectProducts
     */
    private $collectProducts;

    /**
     * @param ResolveProviders $resolveProviders
     * @param CollectProducts  $collectProducts
     */
    public function __construct(
        ResolveProviders $resolveProviders,
        CollectProducts $collectProducts
    )
    {
        $this->resolveProviders = $resolveProviders;
        $this->collectProducts = $collectProducts;
    }

    /**
     * @param Provider $provider
     *
     * @return Product[]
     */
    public function resolve(Provider $provider)
    {
        $products = $this->collectProducts->collect($provider);
        
        return $products;
    }
}
