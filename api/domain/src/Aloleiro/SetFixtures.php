<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Business\ManageStorage as ManageBusinessStorage;
use Muchacuba\Aloleiro\Profile\ManageStorage as ManageProfileStorage;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Cubalider\Call\Provider\Log\ManageStorage as ManageLogStorage;
use Cubalider\Call\Provider\Sinch\ProcessEvent;
use Cubalider\Unique\CreateUniqueness;
use Cubalider\Privilege\CreateProfile as CreatePrivilegeProfile;
use Faker\Factory;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class SetFixtures
{
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
     * @var CreateBusiness
     */
    private $createBusiness;

    /**
     * @var CreatePrivilegeProfile
     */
    private $createPrivilegeProfile;

    /**
     * @var CreateUniqueness
     */
    private $createUniqueness;

    /**
     * @var PromoteUser
     */
    private $promoteUser;

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
     * @param ManageBusinessStorage $manageBusinessStorage
     * @param ManageProfileStorage $manageProfileStorage
     * @param ManagePhoneStorage $managePhoneStorage
     * @param ManageCallStorage $manageCallStorage
     * @param ManageLogStorage $manageLogStorage
     * @param CreateBusiness $createBusiness
     * @param CreateUniqueness $createUniqueness
     * @param CreatePrivilegeProfile $createPrivilegeProfile
     * @param PromoteUser $promoteUser
     * @param AddPhone $addPhone
     * @param PrepareCall $prepareCall
     * @param ProcessEvent $processEvent
     */
    public function __construct(
        ManageBusinessStorage $manageBusinessStorage,
        ManageUniquenessStorage $manageUniquenessStorage,
        ManageProfileStorage $manageProfileStorage,
        ManagePhoneStorage $managePhoneStorage,
        ManageCallStorage $manageCallStorage,
        ManageLogStorage $manageLogStorage,
        CreateBusiness $createBusiness,
        CreateUniqueness $createUniqueness,
        CreatePrivilegeProfile $createPrivilegeProfile,
        PromoteUser $promoteUser,
        AddPhone $addPhone,
        PrepareCall $prepareCall,
        ProcessEvent $processEvent
    )
    {
        $this->manageBusinessStorage = $manageBusinessStorage;
        $this->manageUniquenessStorage = $manageUniquenessStorage;
        $this->manageProfileStorage = $manageProfileStorage;
        $this->managePhoneStorage = $managePhoneStorage;
        $this->manageCallStorage = $manageCallStorage;
        $this->manageLogStorage = $manageLogStorage;
        $this->createBusiness = $createBusiness;
        $this->createUniqueness = $createUniqueness;
        $this->createPrivilegeProfile = $createPrivilegeProfile;
        $this->promoteUser = $promoteUser;
        $this->addPhone = $addPhone;
        $this->prepareCall = $prepareCall;
        $this->processEvent = $processEvent;
    }

    /**
     * @param string $owner
     * @param string $operator
     */
    public function set($owner, $operator)
    {
        $this->purge();

        $this->setBusiness($owner, $operator);

        for ($i = 1; $i <= 10; $i++) {
            $owner = $this->createUniqueness->create();
            $this->createPrivilegeProfile->create($owner, []);
            $operator = $this->createUniqueness->create();
            $this->createPrivilegeProfile->create($operator, []);

            $this->setBusiness($owner, $operator);
        }
    }

    /**
     * @param string $owner
     * @param string $operator
     */
    private function setBusiness($owner, $operator)
    {
        $faker = Factory::create('es_ES');

        $business = $this->createBusiness->create(
            0.0,
            rand(1, 15),
            4412
        );

        $this->promoteUser->promote($owner, $business, 'aloleiro_owner');

        $this->promoteUser->promote($operator, $business, 'aloleiro_operator');

        $phones = [];
        $c = rand(1, 10);
        for ($j = 1; $j <= $c; $j++) {
            $phone = $faker->phoneNumber;
            $phones[$j] = $phone;

            $this->addPhone->add(
                $owner,
                $phone,
                $faker->phoneNumber
            );
        }

        for ($j = 1; $j <= 10; $j++) {
            $from = $phones[rand(1, count($phones))];
            $to = $faker->phoneNumber;

            $this->prepareCall->prepare(
                $operator,
                $from,
                $to
            );

            $cid = uniqid();

            $this->processEvent->process([
                'event' => 'ice',
                'callid' => $cid,
                'cli' => $from,
                'to' => [
                    'endpoint' => '+789'
                ]
            ]);

            $this->processEvent->process([
                'event' => 'ace',
                'callid' => $cid
            ]);

            $this->processEvent->process([
                'event' => 'dice',
                'callid' => $cid,
                'duration' => rand(0, 120),
                'debit' => [
                    'amount' => rand(0, 100) / 100
                ]
            ]);
        }
    }

    private function purge()
    {
        $this->manageBusinessStorage->purge();
        $this->manageProfileStorage->purge();
        $this->managePhoneStorage->purge();
        $this->manageCallStorage->purge();
        $this->manageLogStorage->purge();
    }
}