<?php

namespace Muchacuba\Aloleiro;

use Faker\Generator;
use Muchacuba\Aloleiro\Rate\ManageStorage as ManageRateStorage;
use Muchacuba\Aloleiro\Business\ManageStorage as ManageBusinessStorage;
use Muchacuba\Aloleiro\Profile\ManageStorage as ManageProfileStorage;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Approval\ManageStorage as ManageApprovalStorage;
use Muchacuba\Aloleiro\AdminApproval\ManageStorage as ManageAdminApprovalStorage;
use Cubalider\Call\Provider\Log\ManageStorage as ManageLogStorage;
use Cubalider\Call\Provider\Sinch\ProcessEvent;
use Faker\Factory;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class SetFixtures
{
    /**
     * @var ImportRates
     */
    private $importRates;

    /**
     * @var UpdateVenezuelanCurrencyExchange
     */
    private $updateVenezuelanCurrencyExchange;

    /**
     * @var ManageRateStorage
     */
    private $manageRateStorage;

    /**
     * @var ManageAdminApprovalStorage
     */
    private $manageAdminApprovalStorage;

    /**
     * @var ManageApprovalStorage
     */
    private $manageApprovalStorage;
    
    /**
     * @var ManageBusinessStorage
     */
    private $manageBusinessStorage;

    /**
     * @var ManageProfileStorage
     */
    private $manageProfileStorage;

    /**
     * @var ManagePhoneStorage
     */
    private $managePhoneStorage;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @var ManageLogStorage
     */
    private $manageLogStorage;

    /**
     * @var CreateAdminApproval
     */
    private $createAdminApproval;

    /**
     * @var AddBusiness
     */
    private $addBusiness;
    
    /**
     * @var CreateApproval
     */
    private $createApproval;

    /**
     * @var ListenInitFacebookUser
     */
    private $listenInitFacebookUser;
    
    /**
     * @var AddPhone
     */
    private $addPhone;

    /**
     * @var PrepareCall
     */
    private $prepareCall;

    /**
     * @var ProcessEvent
     */
    private $processEvent;

    /**
     * @var string
     */
    private $testOwnerUniqueness;

    /**
     * @var string
     */
    private $testOwnerEmail;

    /**
     * @var string
     */
    private $testOperatorUniqueness;

    /**
     * @var string
     */
    private $testOperatorEmail;
    
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @param ImportRates $importRates
     * @param UpdateVenezuelanCurrencyExchange $updateVenezuelanCurrencyExchange
     * @param ManageRateStorage $manageRateStorage
     * @param ManageAdminApprovalStorage $manageAdminApprovalStorage
     * @param ManageApprovalStorage $manageApprovalStorage
     * @param ManageBusinessStorage $manageBusinessStorage
     * @param ManageProfileStorage $manageProfileStorage
     * @param ManagePhoneStorage $managePhoneStorage
     * @param ManageCallStorage $manageCallStorage
     * @param ManageLogStorage $manageLogStorage
     * @param CreateAdminApproval $createAdminApproval
     * @param AddBusiness $addBusiness
     * @param CreateApproval $createApproval
     * @param ListenInitFacebookUser $listenInitFacebookUser
     * @param AddPhone $addPhone
     * @param PrepareCall $prepareCall
     * @param ProcessEvent $processEvent
     * @param $testOwnerUniqueness
     * @param $testOwnerEmail
     * @param $testOperatorUniqueness
     * @param $testOperatorEmail
     *
     * @di\arguments({
     *     testOwnerUniqueness:    "%aloleiro_test_owner_uniqueness%",
     *     testOwnerEmail:         "%aloleiro_test_owner_email%",
     *     testOperatorUniqueness: "%aloleiro_test_operator_uniqueness%",
     *     testOperatorEmail:      "%aloleiro_test_operator_email%",
     * })
     */
    public function __construct(
        ImportRates $importRates,
        UpdateVenezuelanCurrencyExchange $updateVenezuelanCurrencyExchange,
        ManageRateStorage $manageRateStorage,
        ManageAdminApprovalStorage $manageAdminApprovalStorage,
        ManageApprovalStorage $manageApprovalStorage,
        ManageBusinessStorage $manageBusinessStorage,
        ManageProfileStorage $manageProfileStorage,
        ManagePhoneStorage $managePhoneStorage,
        ManageCallStorage $manageCallStorage,
        ManageLogStorage $manageLogStorage,
        CreateAdminApproval $createAdminApproval,
        AddBusiness $addBusiness,
        CreateApproval $createApproval,
        ListenInitFacebookUser $listenInitFacebookUser,
        AddPhone $addPhone,
        PrepareCall $prepareCall,
        ProcessEvent $processEvent,
        $testOwnerUniqueness,
        $testOwnerEmail,
        $testOperatorUniqueness,
        $testOperatorEmail
    )
    {
        $this->importRates = $importRates;
        $this->updateVenezuelanCurrencyExchange = $updateVenezuelanCurrencyExchange;
        $this->manageRateStorage = $manageRateStorage;
        $this->manageAdminApprovalStorage = $manageAdminApprovalStorage;
        $this->manageApprovalStorage = $manageApprovalStorage;
        $this->manageBusinessStorage = $manageBusinessStorage;
        $this->manageProfileStorage = $manageProfileStorage;
        $this->managePhoneStorage = $managePhoneStorage;
        $this->manageCallStorage = $manageCallStorage;
        $this->manageLogStorage = $manageLogStorage;
        $this->createAdminApproval = $createAdminApproval;
        $this->addBusiness = $addBusiness;
        $this->createApproval = $createApproval;
        $this->listenInitFacebookUser = $listenInitFacebookUser;
        $this->addPhone = $addPhone;
        $this->prepareCall = $prepareCall;
        $this->processEvent = $processEvent;
        $this->testOwnerUniqueness = $testOwnerUniqueness;
        $this->testOwnerEmail = $testOwnerEmail;
        $this->testOperatorEmail = $testOperatorEmail;
        $this->testOperatorUniqueness = $testOperatorUniqueness;
        $this->faker = Factory::create('es_ES');
    }

    /**
     */
    public function set()
    {
        $this->purge();

        $this->importRates->import();
        $this->updateVenezuelanCurrencyExchange->update();

        $this->createAdminApproval->create();
        
        $business = $this->addBusiness->add(
            5000000,
            15,
            'Test',
            'USA'
        );
        
        $this->createApproval->create(
            $this->testOwnerEmail,
            $business,
            ['aloleiro_owner']
        );

        $this->createApproval->create(
            $this->testOperatorEmail,
            $business,
            ['aloleiro_operator']
        );

        $this->listenInitFacebookUser->listen(
            $this->testOwnerUniqueness,
            $this->testOwnerEmail
        );

        $this->listenInitFacebookUser->listen(
            $this->testOperatorUniqueness,
            $this->testOperatorEmail
        );
        
        $this->setCalls(
            $this->testOwnerUniqueness,
            $this->testOperatorUniqueness
        );
    }

    /**
     * @param string $ownerUniqueness
     * @param string $operatorUniqueness
     */
    private function setCalls($ownerUniqueness, $operatorUniqueness)
    {
        $phones = $this->addPhones(
            $ownerUniqueness,
            rand(1, 10)
        );

        $time = new \DateTime("now");

        /** @var \DateTime[][] $days */
        $days = [];
        for ($i = 1; $i <= 100; $i++) {
            $minutes = [];
            $amount = rand(0, 4); // 0 means that it's a prepared call with no instances
            for ($j = 1; $j <= $amount; $j++) {
                $time->modify(sprintf('- %s minutes', rand(1, 10)));

                $minutes[] = clone $time;
            }
            $days[] = array_reverse($minutes);

            $time->modify(sprintf('- %s days', rand(0, 1)));
        }
        $days = array_reverse($days);

        foreach ($days as $day) {
            $from = $phones[rand(1, count($phones))];
            $to = $this->generatePhoneNumber();

            $this->prepareCall->prepare(
                $operatorUniqueness,
                $from,
                $to
            );

            foreach ($day as $time) {
                $cid = uniqid();

                $this->processEvent->process([
                    'event' => 'ice',
                    'callid' => $cid,
                    'cli' => str_replace('+', '', $from),
                    'to' => [
                        'endpoint' => '+789'
                    ]
                ]);

                $duration = rand(1, 120);

                $this->processEvent->process([
                    'event' => 'ace',
                    'callid' => $cid,
                    'timestamp' => date(
                        DATE_ISO8601,
                        $time->getTimestamp()
                    )
                ]);

                // Some calls will be in progress
                if (rand(0, 1) == 1) {
                    $this->processEvent->process([
                        'event' => 'dice',
                        'callid' => $cid,
                        'timestamp' => date(
                            DATE_ISO8601,
                            $time->getTimestamp() + $duration
                        ),
                        'duration' => $duration,
                        'debit' => [
                            'amount' => rand(1, 100) / 100
                        ]
                    ]);
                }
            }
        }
    }

    private function purge()
    {
        $this->manageRateStorage->purge();
        $this->manageAdminApprovalStorage->purge();
        $this->manageApprovalStorage->purge();
        $this->manageBusinessStorage->purge();
        $this->manageProfileStorage->purge();
        $this->managePhoneStorage->purge();
        $this->manageCallStorage->purge();
        $this->manageLogStorage->purge();
    }

    /**
     * @param string $uniqueness
     * @param int    $amount
     * 
     * @return string[]
     */
    private function addPhones($uniqueness, $amount)
    {
        $phones = [];

        for ($i = 1; $i <= $amount; $i++) {
            $phone = $this->generatePhoneNumber();

            $this->addPhone->add(
                $uniqueness,
                $phone,
                ucfirst($this->faker->colorName)
            );

            $phones[$i] = $phone;
        }

        return $phones;
    }

    /**
     * @return string
     */
    private function generatePhoneNumber()
    {
        return '+' . str_replace(['+', '-', ' '], [''], $this->faker->phoneNumber);
    }
}