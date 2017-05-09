<?php

namespace Muchacuba\Aloleiro\Test;

use Faker\Factory;
use Faker\Generator;
use Muchacuba\Aloleiro\AddPhone as BaseAddPhone;
use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\ExistentPhoneException;
use Muchacuba\Aloleiro\NonExistentPhoneException;
use Muchacuba\Aloleiro\Phone;
use Muchacuba\Aloleiro\Phone\InvalidDataException;
use Muchacuba\Aloleiro\PickPhone as BasePickPhone;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddPhone
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var BasePickPhone
     */
    private $pickPhone;

    /**
     * @var BaseAddPhone
     */
    private $addPhone;

    /**
     * @param BasePickPhone $pickPhone
     * @param BaseAddPhone  $addPhone
     */
    public function __construct(
        BasePickPhone $pickPhone,
        BaseAddPhone $addPhone
    )
    {
        $this->faker = Factory::create('es_ES');
        $this->pickPhone = $pickPhone;
        $this->addPhone = $addPhone;
    }

    /**
     * @param Business    $business
     * @param string|null $number
     * @param string|null $name
     *
     * @return Phone
     *
     * @throws InvalidDataException
     * @throws ExistentPhoneException
     */
    public function add(
        Business $business,
        $number = null,
        $name = null
    ) {
        if ($number === null) {
            $number = $this->generateNumber($business);
        }

        try {
            return $this->addPhone->add(
                $business,
                $number,
                $name !== null ? $name : ucfirst($this->faker->word)
            );
        } catch (InvalidDataException $e) {
            throw $e;
        } catch (ExistentPhoneException $e) {
            throw $e;
        }
    }

    /**
     * @param Business $business
     *
     * @return string
     */
    private function generateNumber(Business $business)
    {
        do {
            $number = $this->faker->phoneNumber;

            try {
                $this->pickPhone->pick($business, $number);

                $ok = false;
            } catch (NonExistentPhoneException $e) {
                $ok = true;
            }
        } while ($ok == false);

        return $number;
    }
}