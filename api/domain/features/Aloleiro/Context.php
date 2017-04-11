<?php

namespace Muchacuba\Aloleiro;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Gherkin\Node\PyStringNode;
use Muchacuba\Aloleiro\Profile\ManageStorage;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;

class Context implements BaseContext, ContainerAwareContext
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Phone[]
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
    public function purgeStorages()
    {
        /** @var ManageStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.aloleiro.profile.manage_storage');
        $manageStorage->purge();

        /** @var ManageStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.aloleiro.phone.manage_storage');
        $manageStorage->purge();

        /** @var ManageStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.aloleiro.call.manage_storage');
        $manageStorage->purge();
    }

    /**
     * @Given there are the profiles with phones:
     *
     * @param PyStringNode $string
     */
    public function thereAreTheProfilesWithPhones(PyStringNode $string)
    {
        $items = json_decode($string->getRaw(), true);

        /** @var CreateProfile $createProfile */
        $createProfile = $this->container->get('muchacuba.aloleiro.create_profile');
        /** @var AddPhone $addPhone */
        $addPhone = $this->container->get('muchacuba.aloleiro.add_phone');

        foreach ($items as $profile) {
            $createProfile->create(
                $profile['uniqueness']
            );

            foreach ($profile['phones'] as $phone) {
                $addPhone->add(
                    $profile['uniqueness'],
                    $phone['number'],
                    $phone['name']
                );
            }
        }
    }

    /**
     * @Given there are the phones:
     *
     * @param PyStringNode $string
     */
    public function thereAreThePhones(PyStringNode $string)
    {
        $items = json_decode($string->getRaw(), true);

        /** @var AddPhone $addPhone */
        $addPhone = $this->container->get('muchacuba.aloleiro.add_phone');

        foreach ($items as $item) {
            $addPhone->add(
                $item['uniqueness'],
                $item['number'],
                $item['name']
            );
        }
    }

    /**
     * @Given there are the calls:
     *
     * @param PyStringNode $string
     */
    public function thereAreTheCalls(PyStringNode $string)
    {
        $items = json_decode($string->getRaw(), true);

        /** @var PrepareCall $prepareCall */
        $prepareCall = $this->container->get('muchacuba.aloleiro.prepare_call');

        foreach ($items as $item) {
            $prepareCall->prepare(
                $item['uniqueness'],
                $item['from'],
                $item['to']
            );
        }
    }
    
    /**
     * @Given I add the phone:
     *
     * @param PyStringNode $string
     */
    public function iAddThePhone(PyStringNode $string)
    {
        $item = json_decode($string->getRaw(), true);

        /** @var AddPhone $addPhone */
        $addPhone = $this->container->get('muchacuba.aloleiro.add_phone');

        try {
            $addPhone->add(
                $item['uniqueness'],
                $item['number'],
                $item['name']
            );
        } catch (ExistentPhoneException $e) {
            $this->error = $e;
        }
    }

    /**
     * @Given I update the phone:
     *
     * @param PyStringNode $string
     */
    public function iUpdateThePhone(PyStringNode $string)
    {
        $item = json_decode($string->getRaw(), true);

        /** @var UpdatePhone $updatePhone */
        $updatePhone = $this->container->get('muchacuba.aloleiro.update_phone');

        try {
            $updatePhone->update(
                $item['uniqueness'],
                $item['number'],
                $item['name']
            );
        } catch (NonExistentPhoneException $e) {
            $this->error = $e;
        }
    }

    /**
     * @Given I remove the phone:
     *
     * @param PyStringNode $string
     */
    public function iRemoveThePhone(PyStringNode $string)
    {
        $item = json_decode($string->getRaw(), true);

        /** @var RemovePhone $removePhone */
        $removePhone = $this->container->get('muchacuba.aloleiro.remove_phone');

        try {
            $removePhone->remove(
                $item['uniqueness'],
                $item['number']
            );
        } catch (NonExistentPhoneException $e) {
            $this->error = $e;
        }
    }

    /**
     * @Given I prepare the call:
     *
     * @param PyStringNode $string
     */
    public function iPrepareTheCall(PyStringNode $string)
    {
        $item = json_decode($string->getRaw(), true);

        /** @var PrepareCall $prepareCall */
        $prepareCall = $this->container->get('muchacuba.aloleiro.prepare_call');

        $prepareCall->prepare(
            $item['uniqueness'],
            $item['from'],
            $item['to']
        );
    }
    
    /**
     * @When I collect the phones from profile ":uniqueness"
     *
     * @param string $uniqueness
     */
    public function iCollectThePhonesFromProfile($uniqueness)
    {
        /** @var CollectPhones $collectPhones */
        $collectPhones = $this->container->get('muchacuba.aloleiro.collect_phones');

        $this->result = $collectPhones->collect($uniqueness);
    }

    /**
     * @When I collect the calls from profile ":uniqueness"
     *
     * @param string $uniqueness
     */
    public function iCollectTheCallsFromProfile($uniqueness)
    {
        /** @var CollectCalls $collectCalls */
        $collectCalls = $this->container->get('muchacuba.aloleiro.collect_calls');

        $this->result = $collectCalls->collect($uniqueness);
    }

    /**
     * @When I process the event:
     *
     * @param PyStringNode $string
     */
    public function iProcessTheEvent(PyStringNode $string)
    {
        $payload = json_decode($string->getRaw(), true);

        /** @var ProcessEvent $processEvent */
        $processEvent = $this->container->get('muchacuba.aloleiro.process_event');

        $this->result = $processEvent->process($payload);
    }

    /**
     * @Then I should get the phones:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldGetThePhones(PyStringNode $string)
    {
        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($this->result), true),
            json_decode($string->getRaw(), true)
        )) {
            throw new \Exception($matcher->getError());
        }
    }

    /**
     * @Then I should get the calls:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldGetTheCalls(PyStringNode $string)
    {
        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($this->result), true),
            json_decode($string->getRaw(), true)
        )) {
            throw new \Exception($matcher->getError());
        }
    }

    /**
     * @Then I should get the response:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldGetTheResponse(PyStringNode $string)
    {
        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($this->result), true),
            json_decode($string->getRaw(), true)
        )) {
            throw new \Exception($matcher->getError());
        }
    }

    /**
     * @Then I should get a nonexistent phone exception
     *
     * @throws \Exception
     */
    public function iShouldGetANonexistentPhoneException()
    {
        if (!$this->error instanceof NonExistentPhoneException) {
            throw new \Exception();
        }
    }

    /**
     * @Then I should get an existent phone exception
     *
     * @throws \Exception
     */
    public function iShouldGetAnExistentPhoneException()
    {
        if (!$this->error instanceof ExistentPhoneException) {
            throw new \Exception();
        }
    }
}
