<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Voip\Nexmo\AnswerCall;
use Cubalider\Voip\Nexmo\ProcessEvent;
use Faker\Generator;
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
     * @var UpdateCurrencies
     */
    private $updateCurrencies;

    /**
     * @var PurgeStorages
     */
    private $purgeStorages;

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
     * @var AnswerCall
     */
    private $answerCall;

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
     * @param UpdateCurrencies $updateCurrencies
     * @param PurgeStorages $purgeStorages
     * @param CreateAdminApproval $createAdminApproval
     * @param AddBusiness $addBusiness
     * @param CreateApproval $createApproval
     * @param ListenInitFacebookUser $listenInitFacebookUser
     * @param AddPhone $addPhone
     * @param PrepareCall $prepareCall
     * @param AnswerCall $answerCall
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
        UpdateCurrencies $updateCurrencies,
        PurgeStorages $purgeStorages,
        CreateAdminApproval $createAdminApproval,
        AddBusiness $addBusiness,
        CreateApproval $createApproval,
        ListenInitFacebookUser $listenInitFacebookUser,
        AddPhone $addPhone,
        PrepareCall $prepareCall,
        AnswerCall $answerCall,
        ProcessEvent $processEvent,
        $testOwnerUniqueness,
        $testOwnerEmail,
        $testOperatorUniqueness,
        $testOperatorEmail
    )
    {
        $this->importRates = $importRates;
        $this->updateCurrencies = $updateCurrencies;
        $this->purgeStorages = $purgeStorages;
        $this->createAdminApproval = $createAdminApproval;
        $this->addBusiness = $addBusiness;
        $this->createApproval = $createApproval;
        $this->listenInitFacebookUser = $listenInitFacebookUser;
        $this->addPhone = $addPhone;
        $this->prepareCall = $prepareCall;
        $this->answerCall = $answerCall;
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
        $this->purgeStorages->purge();

        $this->updateCurrencies->update();
        $this->importRates->import();

        $this->createAdminApproval->create();

        $business = $this->addBusiness->add(
            15,
            5000000,
            'Test',
            'USA'
        );

        $this->createApproval->create(
            $business,
            $this->testOwnerEmail,
            ['aloleiro_owner']
        );

        $this->createApproval->create(
            $business,
            $this->testOperatorEmail,
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
            $business
        );
    }

    /**
     * @param Business $business
     */
    private function setCalls(
        Business $business
    )
    {
        $phones = $this->addPhones(
            $business,
            rand(1, 10)
        );

        $time = new \DateTime("now");

        /** @var \DateTime[][] $days */
        $days = [];
        for ($i = 1; $i <= 20; $i++) {
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
                $business,
                $from,
                $to
            );

            foreach ($day as $time) {
                $id = uniqid('provider-');

                $this->answerCall->answer([
                    'conversation_uuid' => $id,
                    'from' => $from->getNumber(),
                    'to' => '+789',
                ]);

                // Some calls will be in progress
                if (rand(0, 1) == 1) {
                    $duration = rand(1, 120);
                    $start = date(
                        DATE_ISO8601,
                        $time->getTimestamp()
                    );
                    $end = date(
                        DATE_ISO8601,
                        $time->getTimestamp() + $duration
                    );
                    $price = rand(1, 100) / 100;

                    $this->processEvent->process([
                        'status' => 'completed',
                        'direction' => 'outbound',
                        'conversation_uuid' => $id,
                        'start_time' => $start,
                        'end_time' => $end,
                        'duration' => $duration,
                        'price' => $price
                    ]);

                    $this->processEvent->process([
                        'status' => 'completed',
                        'direction' => 'inbound',
                        'conversation_uuid' => $id,
                        'start_time' => $start,
                        'end_time' => $end,
                        'duration' => $duration,
                        'price' => $price
                    ]);
                }
            }
        }
    }

    /**
     * @param Business $business
     * @param int    $amount
     * 
     * @return Phone[]
     */
    private function addPhones(Business $business, $amount)
    {
        $phones = [];

        for ($i = 1; $i <= $amount; $i++) {
            $phone = $this->addPhone->add(
                $business,
                $this->generatePhoneNumber(),
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