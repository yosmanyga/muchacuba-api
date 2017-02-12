<?php

namespace Muchacuba\Mule;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Gherkin\Node\PyStringNode;
use Muchacuba\Google\ResolveGeoPosition;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;

class SearchOffersContext implements BaseContext, ContainerAwareContext
{
    const ADDRESS = "Miami International Airport, NW 42nd Ave, Miami, FL";
    const UP_TO_25_KM = [0, 25];
    const MORE_THAN_25_KM = [25, 40000];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Offer|Offer[]
     */
    private $result;

    /**
     * @var \Exception
     */
    private $error;

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @When there are these offers around an address:
     *
     * @param PyStringNode $string
     */
    public function thereAreTheseOffersAroundAnAddress(PyStringNode $string)
    {
        $items = json_decode($string->getRaw(), true);

        /** @var PurgeOffers $purgeOffers */
        $purgeOffers = $this->container->get('muchacuba.mule.purge_offers');

        $purgeOffers->purge();

        /** @var PopulateOffers $populateOffers */
        $populateOffers = $this->container->get('muchacuba.mule.populate_offers');

        foreach ($items as $item) {
            list($from, $to) = constant(self::class . "::{$item['radius']}");

            $populateOffers->populateAroundAddress(
                self::ADDRESS,
                $from,
                $to,
                $item['amount']
            );
        }
    }

    /**
     * @When I search offers 25 km around that address
     */
    public function iSearchMuleOffers25KmAroundThatAddress()
    {
        /** @var ResolveGeoPosition $resolveGeoPosition */
        $resolveGeoPosition = $this->container->get('muchacuba.google.resolve_geo_position');

        /** @var SearchOffers $searchOffer */
        $searchOffer = $this->container->get('muchacuba.mule.search_offers');

        $addresses = $resolveGeoPosition->resolve(self::ADDRESS);

        $this->result = $searchOffer->search(
            [
                'lat' => $addresses->first()->getCoordinates()->getLatitude(),
                'lng' => $addresses->first()->getCoordinates()->getLongitude()
            ],
            25
        );
    }

    /**
     * @Then I should get :amount offers
     *
     * @param string $amount
     *
     * @throws \Exception
     */
    public function iShouldGetOffers($amount)
    {
        $count = count($this->result);

        if ($count != $amount) {
            throw new \Exception(sprintf("Actual: %s offers", $count));
        }
    }
}
