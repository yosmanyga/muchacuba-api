<?php

namespace Muchacuba\Cli\Topup;

use Muchacuba\Topup\LoadProviders as DomainLoadProviders;

/**
 * @di\command({deductible: true})
 */
class LoadProviders
{
    /**
     * @var DomainLoadProviders
     */
    private $loadProviders;

    /**
     * @param DomainLoadProviders $loadProviders
     */
    public function __construct(DomainLoadProviders $loadProviders)
    {
        $this->loadProviders = $loadProviders;
    }

    /**
     * @cli\resolution({command: "topup.load_providers"})
     */
    public function load()
    {
        $this->loadProviders->load();
    }
}
