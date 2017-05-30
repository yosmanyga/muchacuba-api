<?php

namespace Muchacuba\Http\Topup\Server;

use Symsonte\Http\Server\ConvertParameter;
use Muchacuba\Topup\PickProvider as DomainPickProvider;

/**
 * @di\controller({
 *     deductible: true,
 *     tags: ['symsonte.http.server.convert_parameter']
 * })
 */
class ConvertProviderParameter implements ConvertParameter
{
    /**
     * @var DomainPickProvider
     */
    private $pickProvider;

    /**
     * @param DomainPickProvider $pickProvider
     */
    public function __construct(
        DomainPickProvider $pickProvider
    )
    {
        $this->pickProvider = $pickProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($parameters)
    {
        foreach ($parameters as $name => $parameter) {
            if ($name == 'provider') {
                $provider = $this->pickProvider->pick($parameter);
                $parameters[$name] = $provider;
            }
        }

        return $parameters;
    }
}
