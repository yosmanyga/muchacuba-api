<?php

namespace Muchacuba\Cli\Topup;

use Muchacuba\Topup\ResolveProviders as DomainResolveProviders;

/**
 * @di\command({deductible: true})
 */
class ResolveProviders
{
    /**
     * @var DomainResolveProviders
     */
    private $resolveProviders;

    /**
     * @param DomainResolveProviders $resolveProviders
     */
    public function __construct(DomainResolveProviders $resolveProviders)
    {
        $this->resolveProviders = $resolveProviders;
    }

    /**
     * @cli\resolution({command: "topup.resolve_providers"})
     */
    public function resolve()
    {
        $this->resolveProviders->resolve('05353377172');
    }
}
