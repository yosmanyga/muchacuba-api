<?php

namespace Muchacuba\Cli\Mule;

use Symsonte\Cli\Server;
use Muchacuba\Mule\PopulateOffers as DomainPopulateOffers;

/**
 * @di\command({deductible: true})
 */
class PopulateOffersInMiami
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainPopulateOffers
     */
    private $populateOffers;

    /**
     * @param Server               $server
     * @param DomainPopulateOffers $populateOffers
     */
    public function __construct(
        Server $server,
        DomainPopulateOffers $populateOffers
    )
    {
        $this->server = $server;
        $this->populateOffers = $populateOffers;
    }

    /**
     * @cli\resolution({command: "mule.populate-offers-in-miami"})
     */
    public function promote()
    {
        $this->populateOffers->populateInMiami();
    }
}
