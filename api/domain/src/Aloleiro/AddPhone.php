<?php

namespace Muchacuba\Aloleiro;

use MongoDB\Driver\Exception\BulkWriteException;
use Muchacuba\Aloleiro\Phone\InvalidDataException;
use Muchacuba\Aloleiro\Phone\ManageStorage as ManagePhoneStorage;

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
     * @param ManagePhoneStorage  $managePhoneStorage
     */
    public function __construct(
        ManagePhoneStorage $managePhoneStorage
    )
    {
        $this->managePhoneStorage = $managePhoneStorage;
    }

    /**
     * @param Business $business
     * @param string   $number
     * @param string   $name
     *
     * @return Phone
     *
     * @throws InvalidDataException
     * @throws ExistentPhoneException
     */
    public function add(Business $business, $number, $name)
    {
        try {
            $number = $this->validateNumber($number);
        } catch (InvalidDataException $e) {
            throw $e;
        }

        if (empty($name)) {
            throw new InvalidDataException(
                InvalidDataException::FIELD_NAME
            );
        }

        $phone = new Phone(
            $business->getId(),
            $number,
            $name
        );

        try {
            $this->managePhoneStorage->connect()->insertOne($phone);
        } catch (BulkWriteException $e) {
            if ($e->getWriteResult()->getWriteErrors()[0]->getCode() == 11000) {
                throw new ExistentPhoneException();
            }

            throw $e;
        }

        return $phone;
    }

    /**
     * @param string $number
     *
     * @return string
     *
     * @throws InvalidDataException
     */
    private function validateNumber($number)
    {
        $number = str_replace(['+', '-', ' '], [''], $number);

        if (!ctype_digit($number)) {
            throw new InvalidDataException(
                InvalidDataException::FIELD_NUMBER
            );
        }

        $number = '+' . $number;

        return $number;
    }
}