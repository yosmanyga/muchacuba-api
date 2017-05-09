<?php

namespace Muchacuba\Aloleiro\Test;

use Faker\Factory;
use Faker\Generator;
use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\NonExistentPhoneException;
use Muchacuba\Aloleiro\Phone;
use Muchacuba\Aloleiro\Phone\InvalidDataException;
use Muchacuba\Aloleiro\UpdatePhone as BaseUpdatePhone;
use Muchacuba\Aloleiro\PickPhone as BasePickPhone;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdatePhone
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
     * @var BaseUpdatePhone
     */
    private $updatePhone;

    /**
     * @param BasePickPhone $pickPhone
     * @param BaseUpdatePhone $updatePhone
     */
    public function __construct(
        BasePickPhone $pickPhone,
        BaseUpdatePhone $updatePhone
    )
    {
        $this->faker = Factory::create('es_ES');
        $this->pickPhone = $pickPhone;
        $this->updatePhone = $updatePhone;
    }

    /**
     * @param Business    $business
     * @param string|null $number
     * @param string|null $name
     *
     * @return Phone
     *
     * @throws InvalidDataException
     * @throws NonExistentPhoneException
     */
    public function update(Business $business, $number = null, $name = null)
    {
        if ($number === null) {
            $number = $this->generateNumber($business);
        }

        if ($name === null) {
            try {
                $name = $this->generateName($business, $number);
            } catch (NonExistentPhoneException $e) {
                throw $e;
            }
        }

        try {
            return $this->updatePhone->update($business, $number, $name);
        } catch (InvalidDataException $e) {
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

    /**
     * @param Business $business
     * @param string   $number
     *
     * @return string
     * 
     * @throws NonExistentPhoneException
     */
    private function generateName(Business $business, $number)
    {
        try {
            $phone = $this->pickPhone->pick($business, $number);
        } catch (NonExistentPhoneException $e) {
            throw $e;
        }
        
        do {
            $name = ucfirst($this->faker->word);
        } while ($name == $phone->getName());

        return $name;
    }
}