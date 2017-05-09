<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Phone\InvalidDataException;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;
use MongoDB\UpdateResult;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdatePhone
{
    /**
     * @var ManagePhoneStorage
     */
    private $managePhoneStorage;

    /**
     * @var PickPhone
     */
    private $pickPhone;

    /**
     * @param ManagePhoneStorage $managePhoneStorage
     * @param PickPhone          $pickPhone
     */
    public function __construct(
        ManagePhoneStorage $managePhoneStorage,
        PickPhone $pickPhone
    )
    {
        $this->managePhoneStorage = $managePhoneStorage;
        $this->pickPhone = $pickPhone;
    }

    /**
     * @param Business $business
     * @param string   $number
     * @param string   $name
     *
     * @return Phone
     *
     * @throws InvalidDataException
     * @throws NonExistentPhoneException
     */
    public function update(Business $business, $number, $name)
    {
        if (empty($name)) {
            throw new InvalidDataException(
                InvalidDataException::FIELD_NAME
            );
        }

        /** @var UpdateResult $result */
        $result = $this->managePhoneStorage->connect()->updateOne(
            [
                '_id' => $number,
                'business' => $business->getId()
            ],
            ['$set' => ['name' => $name]]
        );

        if ($result->getMatchedCount() === 0) {
            throw new NonExistentPhoneException();
        }

        return $this->pickPhone->pick($business, $number);
    }
}