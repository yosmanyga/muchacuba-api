<?php

namespace Muchacuba\Aloleiro;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Muchacuba\Aloleiro\Business\InvalidDataException;
use Muchacuba\Aloleiro\Test\CheckAllStates;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;
use Muchacuba\Aloleiro\Test\AddBusiness;

class BusinessContext implements BaseContext, ContainerAwareContext
{
    /**
     * @var CheckAllStates
     */
    private $checkAllStates;

    /**
     * @var CollectBusinesses
     */
    private $collectBusinesses;

    /**
     * @var PickBusiness
     */
    private $pickBusiness;

    /**
     * @var AddBusiness
     */
    private $addBusiness;

    /**
     * @var UpdateBusiness
     */
    private $updateBusiness;

    /**
     * @var mixed
     */
    private $data;

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this->checkAllStates = $container->get(CheckAllStates::class);
        $this->collectBusinesses = $container->get(CollectBusinesses::class);
        $this->pickBusiness = $container->get(PickBusiness::class);
        $this->addBusiness = $container->get(AddBusiness::class);
        $this->updateBusiness = $container->get(UpdateBusiness::class);
    }

    /**
     * @AfterScenario
     *
     * @param AfterScenarioScope $scope
     *
     * @throws \Exception
     */
    public function compareStates(AfterScenarioScope $scope)
    {
        if ($scope->getFeature()->getTitle() != 'Managing businesses') {
            return;
        }

        switch ($scope->getScenario()->getTitle()) {
            case "Add a business":
                $this->checkAllStates->ignore('business');

                break;
            case "Collect businesses":
                $this->checkAllStates->ignore('business');

                break;
        }

        try {
            $this->checkAllStates->compare();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @Given there is a business
     * @Given there is another business
     */
    public function preAddBusiness()
    {
        $this->addBusiness->add(
            null,
            null,
            null,
            null
        );

        $this->checkAllStates->shoot('business');
    }

    /**
     * @Given there are some businesses
     */
    public function preAddBusinesses()
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->addBusiness->add(
                null,
                null,
                null,
                null
            );
        }

        $this->checkAllStates->shoot('business');
    }

    /**
     * @When I add a business
     * @When I add another business
     */
    public function addBusiness()
    {
        $business = $this->addBusiness->add(
            null,
            null,
            null,
            null
        );

        $this->data['expected'][] = $business;
    }

    /**
     * @When I try to add a business using a text as profit balance
     */
    public function tryToAddABusinessUsingATextAsProfitBalance()
    {
        try {
            $this->addBusiness->add(
                'abc',
                null,
                null,
                null
            );
        } catch (InvalidDataException $e) {
            $this->data['actual'] = $e;
        }
    }

    /**
     * @When I try to add a business using a negative number as profit balance
     */
    public function tryToAddBusinessUsingNegativeNumberAsProfitBalance()
    {
        try {
            $this->addBusiness->add(
                -15,
                null,
                null,
                null
            );
        } catch (InvalidDataException $e) {
            $this->data['actual'] = $e;
        }
    }

    /**
     * @When I collect businesses
     */
    public function collectBusinesses()
    {
        $this->data['actual'] = $this->collectBusinesses->collect();
    }

    /**
     * @Then I should get a list with that business
     * @Then I should get a list with those businesses
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
     * @Then I should get an invalid business data exception related to the field profit percent
     *
     * @throws \Exception
     */
    public function expectInvalidDataException()
    {
        $e = $this->data['actual'];

        if (
            !$e instanceof InvalidDataException
            || $e->getField() != 'profitPercent'
        ) {
            throw new \Exception();
        }
    }
}
