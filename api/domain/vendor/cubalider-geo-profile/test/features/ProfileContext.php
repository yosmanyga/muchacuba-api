<?php

namespace Cubalider\Geo;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Gherkin\Node\PyStringNode;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Cubalider\Geo\Profile\ManageStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;

class ProfileContext implements BaseContext, ContainerAwareContext
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Profile|Profiles
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
    public function resetStorage()
    {
        /** @var ManageStorage $manageStorage */
        $manageStorage = $this->container->get('cubalider.geo.profile.manage_storage');

        $manageStorage->purge();
        $manageStorage->prepare();
    }

    /**
     * @Given I create a profile with the following data:
     *
     * @param PyStringNode $string
     */
    public function iCreateAProfileWithTheFollowingData(PyStringNode $string)
    {
        $data = json_decode($string->getRaw(), true);

        /** @var CreateProfile $manager */
        $manager = $this->container->get('cubalider.geo.create_profile');

        try {
            $manager->create($data['uniqueness'], $data['lat'], $data['lng']);
        } catch (ExistentProfileException $e) {
            $this->error = $e;
        }
    }

    /**
     * @When I delete a profile with the following data:
     *
     * @param PyStringNode $string
     */
    public function iDeleteTheProfile(PyStringNode $string)
    {
        $data = json_decode($string->getRaw(), true);

        /** @var DeleteProfile $manager */
        $manager = $this->container->get('cubalider.geo.delete_profile');

        try {
            $manager->delete($data['uniqueness']);
        } catch (NonExistentProfileException $e) {
            $this->error = $e;
        }
    }

    /**
     * @When I pick a profile with the following data:
     *
     * @param PyStringNode $string
     *
     * @throws NonExistentProfileException
     */
    public function iPickTheProfile(PyStringNode $string)
    {
        $data = json_decode($string->getRaw(), true);

        /** @var PickProfile $manager */
        $manager = $this->container->get('cubalider.geo.pick_profile');

        try {
            $this->result = $manager->pick($data['uniqueness']);
        } catch (NonExistentProfileException $e) {
            $this->error = $e;
        }
    }

    /**
     * @When I collect the profiles
     */
    public function iCollectTheProfiles()
    {
        /** @var CollectProfiles $manager */
        $manager = $this->container->get('cubalider.geo.collect_profiles');

        $this->result = $manager->collect();
    }

    /**
     * @Then I should get the following data:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldGetTheFollowingData(PyStringNode $string)
    {
        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($this->result), true),
            json_decode((string) $string->getRaw(), true)
        )
        ) {
            throw new \Exception($matcher->getError());
        }
    }

    /**
     * @Then I should get an existent profile exception
     *
     * @throws \Exception
     */
    public function iShouldGetAnExistentProfileException()
    {
        if (!$this->error instanceof ExistentProfileException) {
            throw new \Exception();
        }
    }

    /**
     * @Then I should get a nonexistent profile exception
     *
     * @throws \Exception
     */
    public function iShouldGetANonexistentProfileException()
    {
        if (!$this->error instanceof NonExistentProfileException) {
            throw new \Exception();
        }
    }
}
