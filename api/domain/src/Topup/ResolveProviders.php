<?php

namespace Muchacuba\Topup;

use Cubalider\Phone\FixNumber;
use Cubalider\Phone\InvalidNumberException;
use Muchacuba\Topup\Recharge\InvalidAccountException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ResolveProviders
{
    /**
     * @var FixNumber
     */
    private $fixNumber;

    /**
     * @var CollectProviders
     */
    private $collectProviders;

    /**
     * @param FixNumber        $fixNumber
     * @param CollectProviders $collectProviders
     */
    public function __construct(
        FixNumber $fixNumber,
        CollectProviders $collectProviders
    )
    {
        $this->fixNumber = $fixNumber;
        $this->collectProviders = $collectProviders;
    }

    /**
     * Needs country. This incomplete number from Cuba 53234234 matches Ecuador
     * providers.
     *
     * @param string $country
     * @param string $prefix
     * @param string $account
     * 
     * @return Provider[]
     *
     * @throws InvalidAccountException
     */
    public function resolve($country, $prefix, $account)
    {
        try {
            $account = $this->fixNumber->fix($account);
            $account = str_replace('+', '', $account);
        } catch (InvalidNumberException $e) {
            throw new InvalidAccountException();
        }

        $providers = [];
        foreach ($this->collectProviders->collect($country) as $provider) {
            if (preg_match(
                sprintf('/%s/', $provider->getValidation()),
                sprintf('%s%s', $prefix, $account)
            ) === 1) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }
}
