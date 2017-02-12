<?php

namespace Muchacuba\Mule;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Gherkin\Node\PyStringNode;
use Muchacuba\Mule\Offer\InvalidDataException;
use Muchacuba\Mule\Offer\PurgeStorage as PurgeOfferStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;

class ManageOfferContext implements BaseContext, ContainerAwareContext
{
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
     * @BeforeScenario
     */
    public function purgeStorage()
    {
        /** @var PurgeOfferStorage $purgeOfferStorage */
        $purgeOfferStorage = $this->container->get('muchacuba.mule.offer.purge_storage');

        $purgeOfferStorage->purge();
    }

    /**
     * @Given I insert an offer:
     *
     * @param PyStringNode $string
     */
    public function iInsertAnOffer(PyStringNode $string)
    {
        $item = json_decode($string->getRaw(), true);

        /** @var ManageOffer $manageOffer */
        $manageOffer = $this->container->get('muchacuba.mule.manage_offer');

        try {
            $manageOffer->insert(
                null,
                $item['name'],
                $item['contact'],
                $item['address'],
                $item['coordinates'],
                $item['destinations'],
                $item['description'],
                $item['trips']
            );
        } catch (InvalidDataException $e) {
            $this->error = $e;
        }
    }

    /**
     * @Given I update offer :id:
     *
     * @param string       $id
     * @param PyStringNode $string
     */
    public function iUpdateMuleOffer($id, PyStringNode $string)
    {
        $item = json_decode($string->getRaw(), true);

        /** @var ManageOffer $manageOffer */
        $manageOffer = $this->container->get('muchacuba.mule.manage_offer');

        try {
            $manageOffer->update(
                $id,
                $item['name'],
                $item['contact'],
                $item['address'],
                $item['coordinates'],
                $item['destinations'],
                $item['description'],
                $item['trips']
            );
        } catch (InvalidDataException $e) {
            $this->error = $e;
        } catch (NonExistentOfferException $e) {
            $this->error = $e;
        }
    }

    /**
     * @Given I delete offer :id
     *
     * @param string $id
     */
    public function iDeleteMuleOffer($id)
    {
        /** @var ManageOffer $manageOffer */
        $manageOffer = $this->container->get('muchacuba.mule.manage_offer');

        try {
            $manageOffer->delete($id);
        } catch (NonExistentOfferException $e) {
            $this->error = $e;
        }
    }

    /**
     * @Then I should have :amount offer
     * @Then I should have :amount offers
     *
     * @param int $amount
     *
     * @throws \Exception
     */
    public function iShouldHaveXOffers($amount)
    {
        if ($this->error !== null) {
            throw $this->error;
        }

        /** @var CountOffers $countOffers */
        $countOffers = $this->container->get('muchacuba.mule.count_offers');
        $count = $countOffers->count();

        if ($amount != $count) {
            throw new \Exception(sprintf("There are %s offers", $count));
        }
    }
}
