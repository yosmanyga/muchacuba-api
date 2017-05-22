<?php

namespace Muchacuba\Topup;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ResolveProviders
{
    /**
     * @var CollectProviders
     */
    private $collectProviders;

    /**
     * @param CollectProviders $collectProviders
     */
    public function __construct(
        CollectProviders $collectProviders
    )
    {
        $this->collectProviders = $collectProviders;
    }

    /**
     * @param string $account
     * 
     * @return Provider[]
     */
    public function resolve($account)
    {
        $providers = [];
        foreach ($this->collectProviders->collect() as $provider) {
            if (preg_match(sprintf('/%s/', $provider->getValidation()), $account) === 1) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }
}
