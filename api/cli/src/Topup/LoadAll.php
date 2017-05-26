<?php

namespace Muchacuba\Cli\Topup;

use Muchacuba\Topup\LoadProviders as DomainLoadProviders;
use Muchacuba\Topup\LoadProducts as DomainLoadProducts;
use Muchacuba\Topup\LoadPromotions as DomainLoadPromotions;
use Muchacuba\Topup\LoadCountries as DomainLoadCountries;

/**
 * @di\command({deductible: true})
 */
class LoadAll
{
    /**
     * @var DomainLoadProviders
     */
    private $loadProviders;

    /**
     * @var DomainLoadProducts
     */
    private $loadProducts;

    /**
     * @var DomainLoadPromotions
     */
    private $loadPromotions;
    
    /**
     * @var DomainLoadCountries
     */
    private $loadCountries;

    /**
     * @param DomainLoadProviders  $loadProviders
     * @param DomainLoadProducts   $loadProducts
     * @param DomainLoadPromotions $loadPromotions
     * @param DomainLoadCountries  $loadCountries
     */
    public function __construct(
        DomainLoadProviders $loadProviders,
        DomainLoadProducts $loadProducts,
        DomainLoadPromotions $loadPromotions,
        DomainLoadCountries $loadCountries
    )
    {
        $this->loadProviders = $loadProviders;
        $this->loadProducts = $loadProducts;
        $this->loadPromotions = $loadPromotions;
        $this->loadCountries = $loadCountries;
    }

    /**
     * @cli\resolution({command: "topup.load_all"})
     */
    public function load()
    {
        $this->loadProviders->load();
        $this->loadProducts->load();
        $this->loadPromotions->load();
        $this->loadCountries->load();
    }
}
