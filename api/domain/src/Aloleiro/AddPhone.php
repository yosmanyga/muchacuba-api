<?php

namespace Muchacuba\Aloleiro;

use MongoDB\Driver\Exception\BulkWriteException;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;
use Muchacuba\Aloleiro\Profile\ManageStorage as ManageProfileStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddPhone
{
    /**
     * @var ManagePhoneStorage
     */
    private $managePhoneStorage;

    /**
     * @var ManageProfileStorage
     */
    private $manageProfileStorage;

    /**
     * @param ManagePhoneStorage   $managePhoneStorage
     * @param ManageProfileStorage $manageProfileStorage
     */
    public function __construct(
        ManagePhoneStorage $managePhoneStorage,
        ManageProfileStorage $manageProfileStorage
    )
    {
        $this->managePhoneStorage = $managePhoneStorage;
        $this->manageProfileStorage = $manageProfileStorage;
    }

    /**
     * @param string $uniqueness
     * @param string $number
     * @param string $name
     *
     * @throws ExistentPhoneException
     */
    public function add($uniqueness, $number, $name)
    {
        try {
            $this->managePhoneStorage->connect()->insertOne(new Phone(
                $number,
                $name
            ));
        } catch (BulkWriteException $e) {
            if ($e->getWriteResult()->getWriteErrors()[0]->getCode() == 11000) {
                throw new ExistentPhoneException();
            }

            throw $e;
        }

        $this->manageProfileStorage->connect()->updateOne(
            ['_id' => $uniqueness],
            ['$push' => ['phones' => $number]]
        );
    }
}