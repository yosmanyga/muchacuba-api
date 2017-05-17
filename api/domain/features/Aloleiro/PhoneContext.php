<?php

namespace Muchacuba\Aloleiro;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Muchacuba\Aloleiro\Phone\InvalidDataException;
use Muchacuba\Aloleiro\Test\CheckAllStates;
use Muchacuba\Aloleiro\Test\PickLastBusiness;
use Muchacuba\Aloleiro\Test\PickLastPhone;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;
use Muchacuba\Aloleiro\Test\AddPhone;
use Muchacuba\Aloleiro\Test\UpdatePhone;

class PhoneContext implements BaseContext, ContainerAwareContext
{
    /**
     * @var CheckAllStates
     */
    private $checkAllStates;

    /**
     * @var AddBusiness
     */
    private $addBusiness;

    /**
     * @var CollectBusinesses
     */
    private $collectBusinesses;

    /**
     * @var AddPhone
     */
    private $addPhone;

    /**
     * @var PickLastBusiness
     */
    private $pickLastBusiness;

    /**
     * @var PickLastPhone
     */
    private $pickLastPhone;

    /**
     * @var CollectPhones
     */
    private $collectPhones;

    /**
     * @var UpdatePhone
     */
    private $updatePhone;

    /**
     * @var RemovePhone
     */
    private $removePhone;

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
        $this->addBusiness = $container->get(AddBusiness::class);
        $this->collectBusinesses = $container->get(CollectBusinesses::class);
        $this->addPhone = $container->get(AddPhone::class);
        $this->pickLastBusiness = $container->get(PickLastBusiness::class);
        $this->pickLastPhone = $container->get(PickLastPhone::class);
        $this->collectPhones = $container->get(CollectPhones::class);
        $this->updatePhone = $container->get(UpdatePhone::class);
        $this->removePhone = $container->get(RemovePhone::class);
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
        if ($scope->getFeature()->getTitle() != 'Managing phones') {
            return;
        }

        switch ($scope->getScenario()->getTitle()) {
            case "Add a phone":
                $this->checkAllStates->ignore('phone');

                break;
            case "Collect phones from a business":
                $this->checkAllStates->ignore('phone');

                break;
            case "Update a phone":
                $this->checkAllStates->ignore('phone');

                break;
        }

        try {
            $this->checkAllStates->compare();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @Given there is a phone in that business
     * @Given there is another phone in that business
     */
    public function preAddPhone()
    {
        $business = $this->pickLastBusiness->pick();

        $this->addPhone->add(
            $business,
            null,
            null
        );

        $this->checkAllStates->shoot('phone');
    }

    /**
     * @Given there is a phone from Venezuela in that business
     */
    public function preAddPhoneFromVenezuela()
    {
        $business = $this->pickLastBusiness->pick();

        $this->addPhone->add(
            $business,
            '+582819962120',
            null
        );

        $this->checkAllStates->shoot('phone');
    }

    /**
     * @When I add a phone to that business
     * @When I add another phone to that business
     */
    public function addPhone()
    {
        $business = $this->pickLastBusiness->pick();

        $phone = $this->addPhone->add(
            $business,
            null,
            null
        );

        $this->data['expected'][] = $phone;
    }

    /**
     * @When I try to add a phone using same number in that business
     */
    public function tryToAddPhoneUsingSameNumber()
    {
        $business = $this->pickLastBusiness->pick();
        $phone = $this->pickLastPhone->pick();

        try {
            $this->addPhone->add(
                $business,
                $phone->getNumber(),
                null
            );
        } catch (ExistentPhoneException $e) {
            $this->data['actual'] = $e;
        }
    }

    /**
     * @When I try to add a phone with empty text as name in that business
     */
    public function tryToAddPhoneUsingEmptyTextAsName()
    {
        $business = $this->pickLastBusiness->pick();

        try {
            $this->addPhone->add(
                $business,
                null,
                ''
            );
        } catch (InvalidDataException $e) {
            $this->data['actual'] = $e;
        }
    }

    /**
     * @When I collect phones on that business 
     */
    public function collectPhonesFromLastBusiness()
    {
        $business = $this->pickLastBusiness->pick();
        
        $this->data['actual'] = $this->collectPhones->collect($business);
    }
    
    /**
     * @When I update that phone
     */
    public function updateLastPhone()
    {
        $business = $this->pickLastBusiness->pick();
        $phone = $this->pickLastPhone->pick($business);

        $phone = $this->updatePhone->update(
            $business,
            $phone->getNumber(),
            null
        );

        $this->data['expected'][] = [
            'business' => $business->getId(),
            'number' => $phone->getNumber(),
            'name' => $phone->getName()
        ];
    }

    /**
     * @When I try to update a phone on that business
     */
    public function tryToUpdatePhoneUsingAnotherNumber()
    {
        $business = $this->pickLastBusiness->pick();

        try {
            $this->updatePhone->update(
                $business,
                null,
                null
            );
        } catch (NonExistentPhoneException $e) {
            $this->data['actual'] = $e;
        }
    }

    /**
     * @When I try to update that phone using another business
     */
    public function tryToUpdatePhoneUsingAnotherBusiness()
    {
        $business = $this->pickLastBusiness->pick();

        $businesses = $this->collectBusinesses->collect();
        $i = 0;
        do {
            $anotherBusiness = $businesses[$i];
            $i++;

            // Condition to avoid an infinite loop due to some bug
            if ($i == count($businesses)) {
                throw new \Exception();
            }
        } while ($anotherBusiness->getId() == $business->getId());

        $phone = $this->pickLastPhone->pick($business);

        try {
            $this->updatePhone->update(
                $anotherBusiness,
                $phone->getNumber(),
                null
            );
        } catch (NonExistentPhoneException $e) {
            $this->data['actual'] = $e;
        }
    }

    /**
     * @When I try to update that phone using an empty text as name
     */
    public function tryToUpdatePhoneUsingAnEmptyTextAsName()
    {
        $business = $this->pickLastBusiness->pick();
        $phone = $this->pickLastPhone->pick($business);

        try {
            $this->updatePhone->update(
                $business,
                $phone->getNumber(),
                ''
            );
        } catch (InvalidDataException $e) {
            $this->data['actual'] = $e;
        }
    }

    /**
     * @Then I should get a list with that phone
     * @Then I should get a list with those phones
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
     * @Then I should get an invalid phone data exception related to the field name
     *
     * @throws \Exception
     */
    public function expectInvalidDataException()
    {
        $e = $this->data['actual'];

        if (
            !$e instanceof InvalidDataException
            || $e->getField() != 'name'
        ) {
            throw new \Exception();
        }
    }

    /**
     * @Then I should get an existent phone exception
     *
     * @throws \Exception
     */
    public function expectExistentPhoneException()
    {
        if (!$this->data['actual'] instanceof ExistentPhoneException) {
            throw new \Exception();
        }
    }

    /**
     * @Then I should get a nonexistent phone exception
     *
     * @throws \Exception
     */
    public function expectNonexistentPhoneException()
    {
        if (!$this->data['actual'] instanceof NonexistentPhoneException) {
            throw new \Exception();
        }
    }
}
