<?php

namespace Muchacuba\Cli\Topup;

use Muchacuba\Topup\LoadProducts as DomainLoadProducts;

/**
 * @di\command({deductible: true})
 */
class LoadProducts
{
    /**
     * @var DomainLoadProducts
     */
    private $loadProducts;

    /**
     * @param DomainLoadProducts $loadProducts
     */
    public function __construct(DomainLoadProducts $loadProducts)
    {
        $this->loadProducts = $loadProducts;
    }

    /**
     * @cli\resolution({command: "topup.load_products"})
     */
    public function load()
    {
        $this->loadProducts->load();
    }
}
