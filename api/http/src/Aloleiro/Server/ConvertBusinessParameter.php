<?php

namespace Muchacuba\Http\Aloleiro\Server;

use Symsonte\Http\Server\ConvertParameter;
use Muchacuba\Aloleiro\PickProfile as DomainPickProfile;
use Muchacuba\Aloleiro\PickBusiness as DomainPickBusiness;

/**
 * @di\controller({
 *     deductible: true,
 *     tags: ['symsonte.http.server.convert_parameter']
 * })
 */
class ConvertBusinessParameter implements ConvertParameter
{
    /**
     * @var DomainPickProfile
     */
    private $pickProfile;

    /**
     * @var DomainPickBusiness
     */
    private $pickBusiness;

    /**
     * @param DomainPickProfile  $pickProfile
     * @param DomainPickBusiness $pickBusiness
     */
    public function __construct(
        DomainPickProfile $pickProfile,
        DomainPickBusiness $pickBusiness
    )
    {
        $this->pickProfile = $pickProfile;
        $this->pickBusiness = $pickBusiness;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($parameters)
    {
        foreach ($parameters as $name => $parameter) {
            if ($name == 'business') {
                $profile = $this->pickProfile->pick($parameters['uniqueness']);
                $business = $this->pickBusiness->pick($profile->getBusiness());
                $parameters[$name] = $business;
            }
        }

        return $parameters;
    }
}
