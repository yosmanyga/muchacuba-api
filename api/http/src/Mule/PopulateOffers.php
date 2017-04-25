<?php

namespace Muchacuba\Http\Mule;

use Muchacuba\Mule\PopulateOffers as DomainPopulateOffers;

/**
 * @di\controller({deductible: true})
 */
class PopulateOffers
{
    /**
     * @var DomainPopulateOffers
     */
    private $populateOffers;

    /**
     * @param DomainPopulateOffers $populateOffers
     */
    public function __construct(
        DomainPopulateOffers $populateOffers
    ) {
        $this->populateOffers = $populateOffers;
    }

    /**
     * @http\resolution({method: "GET", uri: "/mule/populate-offers-in-miami"})
     * @http\authorization({roles: ["mule_admin"]})
     */
    public function populateInMiami()
    {
        $this->populateOffers->populateInMiami();
    }
}
