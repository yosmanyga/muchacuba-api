<?php

namespace Muchacuba\Aloleiro;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Gherkin\Node\PyStringNode;
use Cubalider\Call\Provider\CollectLogs;
use Cubalider\Call\Provider\Sinch\CollectRequests;
use Cubalider\Call\Provider\Sinch\ProcessEvent;
use Muchacuba\Aloleiro\Profile\ManageStorage as ManageProfileStorage;
use Muchacuba\Aloleiro\Business\ManageStorage as ManageBusinessStorage;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Cubalider\Call\Provider\Log\ManageStorage as ManageLogStorage;
use Cubalider\Call\Provider\Sinch\Request\ManageStorage as ManageRequestStorage;
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
        /** @var ManageProfileStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.aloleiro.business.manage_storage');
        $manageStorage->purge();

        /** @var ManageBusinessStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.aloleiro.profile.manage_storage');
        $manageStorage->purge();

        /** @var ManagePhoneStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.aloleiro.phone.manage_storage');
        $manageStorage->purge();

        /** @var ManageCallStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.aloleiro.call.manage_storage');
        $manageStorage->purge();

        /** @var ManageLogStorage $manageStorage */
        $manageStorage = $this->container->get('cubalider.call.provider.log.manage_storage');
        $manageStorage->purge();

        /** @var ManageRequestStorage $manageStorage */
        $manageStorage = $this->container->get('cubalider.call.provider.sinch.request.manage_storage');
        $manageStorage->purge();
    }

    /**
     * @Given there is the business ":id"
     *
     * @param string $id
     */
    public function thereIsTheBusiness($id)
    {
        /** @var CreateBusiness $createBusiness */
        $createBusiness = $this->container->get('muchacuba.aloleiro.create_business');

        $createBusiness->create(null, $id);
    }

    /**
     * @Given there is the profile:
     *
     * @param PyStringNode $string
     */
    public function thereIsTheProfile(PyStringNode $string)
    {
        $item = json_decode($string->getRaw(), true);
        
        /** @var CreateProfile $createProfile */
        $createProfile = $this->container->get('muchacuba.aloleiro.create_profile');

        $createProfile->create(
            $item['uniqueness'], 
            $item['business']
        );
    }

    /**
     * @Given there are the phones on business that the profile ":uniqueness" belongs to:
     *
     * @param string       $uniqueness
     * @param PyStringNode $string
     */
    public function thereAreThePhones($uniqueness, PyStringNode $string)
    {
        $items = json_decode($string->getRaw(), true);

        /** @var AddPhone $addPhone */
        $addPhone = $this->container->get('muchacuba.aloleiro.add_phone');

        foreach ($items as $phone) {
            $addPhone->add(
                $uniqueness,
                $phone['number'],
                $phone['name']
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
     * @Given I prepare the call from the profile ":uniqueness":
     *
     * @param string       $uniqueness
     * @param PyStringNode $string
     */
    public function iPrepareTheCall($uniqueness, PyStringNode $string)
    {
        $item = json_decode($string->getRaw(), true);

        /** @var PrepareCall $prepareCall */
        $prepareCall = $this->container->get('muchacuba.aloleiro.prepare_call');

        $prepareCall->prepare(
            $uniqueness,
            $item['from'],
            $item['to']
        );
    }
    
    /**
     * @When I collect the phones using profile ":uniqueness"
     *
     * @param string $uniqueness
     */
    public function iCollectThePhonesUsingProfile($uniqueness)
    {
        /** @var CollectPhones $collectPhones */
        $collectPhones = $this->container->get('muchacuba.aloleiro.collect_phones');

        $this->result = $collectPhones->collect($uniqueness);
    }

    /**
     * @When I collect the system calls from profile ":uniqueness"
     *
     * @param string $uniqueness
     */
    public function iCollectTheSystemCallsFromProfile($uniqueness)
    {
        /** @var CollectSystemCalls $collectSystemCalls */
        $collectSystemCalls = $this->container->get('muchacuba.aloleiro.collect_system_calls');

        $this->result = $collectSystemCalls->collect($uniqueness);
    }

    /**
     * @When I collect the logs
     */
    public function iCollectTheLogs()
    {
        /** @var CollectLogs $collectLogs */
        $collectLogs = $this->container->get('cubalider.call.provider.collect_logs');

        $this->result = $collectLogs->collect();
    }

    /**
     * @When I collect the sinch requests
     */
    public function iCollectTheRequests()
    {
        /** @var CollectRequests $collectRequests */
        $collectRequests = $this->container->get('cubalider.call.provider.sinch.collect_requests');

        $this->result = $collectRequests->collect();
    }
    
    /**
     * @When I process the sinch event:
     *
     * @param PyStringNode $string
     */
    public function iProcessTheSinchEvent(PyStringNode $string)
    {
        $payload = json_decode($string->getRaw(), true);

        /** @var ProcessEvent $processEvent */
        $processEvent = $this->container->get('cubalider.call.provider.sinch.process_event');

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
     * @Then I should get the system calls:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldGetTheSystemCalls(PyStringNode $string)
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
     * @Then I should send the response:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldSendTheResponse(PyStringNode $string)
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
     * @Then I should send no response
     *
     * @throws \Exception
     */
    public function iShouldSendNoResponse()
    {
        $matcher = (new SimpleFactory())->createMatcher();

        if ($this->result != null) {
            throw new \Exception($matcher->getError());
        }
    }

    /**
     * @Then I should get the logs:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldGetTheLogs(PyStringNode $string)
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
     * @Then I should get the sinch requests:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldGetTheSinchRequests(PyStringNode $string)
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
