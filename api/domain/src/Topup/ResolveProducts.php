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
     * @var CollectProducts
     */
    private $collectProducts;

    /**
     * @param CollectProducts $collectProducts
     */
    public function __construct(
        CollectProducts $collectProducts
    )
    {
        $this->collectProducts = $collectProducts;
    }

    /**
     * @param Provider $provider
     * 
     * @return Product[]
     */
    public function resolve(Provider $provider)
    {
        $products = [];
        foreach ($this->collectProducts->collect() as $product) {
            if ($product->getProvider() == $provider->getId()) {
                $products[] = $product;
            }
        }

        return $products;
    }
}
