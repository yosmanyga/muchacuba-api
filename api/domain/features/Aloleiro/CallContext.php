<?php

namespace Muchacuba\Aloleiro;

use Behat\Behat\Context\Context as BaseContext;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Cubalider\Voip\Nexmo\Test\AnswerCall;
use Faker\Factory;
use Faker\Generator;
use Muchacuba\Aloleiro\Business\InsufficientBalanceException;
use Muchacuba\Aloleiro\Call\InvalidDataException;
use Muchacuba\Aloleiro\Test\PickLastBusiness;
use Muchacuba\Aloleiro\Test\PickLastPhone;
use Muchacuba\Aloleiro\Test\PickLastCall;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;
use Muchacuba\Aloleiro\Test\PrepareCall;

class CallContext implements BaseContext, ContainerAwareContext
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var CollectBusinesses
     */
    private $collectBusinesses;

    /**
     * @var CollectPhones
     */
    private $collectPhones;

    /**
     * @var PickLastBusiness
     */
    private $pickLastBusiness;

    /**
     * @var PickLastPhone
     */
    private $pickLastPhone;

    /**
     * @var PickLastCall
     */
    private $pickLastCall;

    /**
     * @var PrepareCall
     */
    private $prepareCall;

    /**
     * @var CollectCalls
     */
    private $collectCalls;

    /**
     * @var CollectSystemCalls
     */
    private $collectSystemCalls;

    /**
     * @var AnswerCall
     */
    private $answerCall;

    /**
     * @var mixed
     */
    private $data;

    /**
     */
    public function __construct()
    {
        $this->faker = Factory::create('es_ES');
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this->collectBusinesses = $container->get(CollectBusinesses::class);
        $this->collectPhones = $container->get(CollectPhones::class);
        $this->pickLastBusiness = $container->get(PickLastBusiness::class);
        $this->pickLastPhone = $container->get(PickLastPhone::class);
        $this->pickLastCall = $container->get(PickLastCall::class);
        $this->prepareCall = $container->get(PrepareCall::class);
        $this->collectCalls = $container->get(CollectCalls::class);
        $this->collectSystemCalls = $container->get(CollectSystemCalls::class);
        $this->answerCall = $container->get(AnswerCall::class);
    }

    /**
     * @Given there is a prepared call for that phone
     */
    public function prePrepareCall()
    {
        $business = $this->pickLastBusiness->pick();
        $phone = $this->pickLastPhone->pick($business);

        $this->prepareCall->prepare(
            $business,
            $phone,
            $this->faker->phoneNumber
        );
    }

    /**
     * @Given there are some prepared calls
     */
    public function prePrepareCalls()
    {
        $businesses = $this->collectBusinesses->collect();

        for ($i = 1; $i <= 10; $i++) {
            $business = $businesses[rand(0, count($businesses) - 1)];
            $phones = $this->collectPhones->collect($business);
            $phone = $phones[rand(0, count($phones) - 1)];

            $this->prepareCall->prepare(
                $business,
                $phone,
                $this->faker->phoneNumber
            );
        }
    }

    /**
     * @When I prepare a call on that business using that phone
     */
    public function prepareCall()
    {
        $business = $this->pickLastBusiness->pick();
        $phone = $this->pickLastPhone->pick($business);

        $call = $this->prepareCall->prepare(
            $business,
            $phone,
            $this->faker->phoneNumber
        );

        $this->data['expected'][] = $call;
    }

    /**
     * @When I collect prepared calls on that business
     */
    public function collectCalls()
    {
        $business = $this->pickLastBusiness->pick();

        $this->data['actual'] = $this->collectCalls->collect($business);
    }

    /**
     * @When I receive a nexmo answer call, having the specified phone in that prepared call
     */
    public function answerCall()
    {
        $business = $this->pickLastBusiness->pick();
        $call = $this->pickLastCall->pick($business);

        $response = $this->answerCall->answer(
            $call->getFrom()
        );

        $this->data['expected'] = $response;
    }

    /**
     * @When I receive a nexmo answer call from Venezuela, having the specified phone without the country code in that prepared call
     */
    public function answerCallFromVenezuela()
    {
        $business = $this->pickLastBusiness->pick();
        $call = $this->pickLastCall->pick($business);

        $response = $this->answerCall->answer(
            str_replace('+58', '', $call->getFrom()),
            '+582123353020'
        );

        $this->data['expected'] = $response;
    }

    /**
     * @When I receive a nexmo answer call, having another phone than that prepared call
     */
    public function answerCallFromAnotherPhone()
    {
        $response = $this->answerCall->answer();

        $this->data['expected'] = $response;
    }

    /**
     * @Then I should get a list with that call
     *
     * @throws \Exception
     */
    public function compare()
    {
        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($this->data['expected']), true),
            json_decode(json_encode($this->data['actual']), true)
        )) {
            throw new \Exception($matcher->getError());
        }
    }

    /**
     * @Then I should get a response to nexmo, ordering to connect to the specified number in that prepared call
     *
     * @throws \Exception
     */
    public function compareNexmoConnectResponse()
    {
        $business = $this->pickLastBusiness->pick();
        $call = $this->pickLastCall->pick($business);

        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            $this->data['expected'],
            [
                [
                    'action' => 'talk',
                    'text' => 'Por favor, espere mientras le comunicamos',
                    'voiceName' => 'Conchita'
                ],
                [
                    'action' => 'connect',
                    'from' => $call->getFrom(),
                    'endpoint' => [
                        [
                            'type' => 'phone',
                            'number' => $call->getTo()
                        ]
                    ]
                ]
            ]
        )) {
            throw new \Exception($matcher->getError());
        }
    }

    /**
     * @Then I should get a response to nexmo, ordering to talk a no available message
     *
     * @throws \Exception
     */
    public function compareNexmoHangupResponse()
    {
        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            $this->data['expected'],
            [
                [
                    'action' => 'talk',
                    'text' => 'No disponible',
                    'voiceName' => 'Conchita'
                ]
            ]
        )) {
            throw new \Exception($matcher->getError());
        }
    }

    /**
     * @Then I should get an invalid call data exception
     *
     * @throws \Exception
     */
    public function iShouldGetAnInvalidCallDataException()
    {
        if (!$this->data['actual'] instanceof InvalidDataException) {
            throw new \Exception();
        }
    }

    /**
     * @Then I should get an insufficient balance exception
     *
     * @throws \Exception
     */
    public function iShouldGetAnInsufficientBalanceException()
    {
        if (!$this->data['actual'] instanceof InsufficientBalanceException) {
            throw new \Exception();
        }
    }
}
