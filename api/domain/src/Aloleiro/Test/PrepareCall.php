<?php

namespace Muchacuba\Aloleiro\Test;

use Faker\Factory;
use Faker\Generator;
use Muchacuba\Aloleiro\Call;
use Muchacuba\Aloleiro\PrepareCall as BasePrepareCall;
use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\ExistentPhoneException;
use Muchacuba\Aloleiro\Phone;
use Muchacuba\Aloleiro\Phone\InvalidDataException;
use Muchacuba\Aloleiro\Business\InsufficientBalanceException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PrepareCall
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var BasePrepareCall
     */
    private $prepareCall;

    /**
     * @param BasePrepareCall  $prepareCall
     */
    public function __construct(
        BasePrepareCall $prepareCall
    )
    {
        $this->faker = Factory::create('es_ES');
        $this->prepareCall = $prepareCall;
    }

    /**
     * @param Business    $business
     * @param Phone       $phone
     * @param string|null $to
     *
     * @return Call
     *
     * @throws InvalidDataException
     * @throws InsufficientBalanceException
     */
    public function prepare(
        Business $business,
        Phone $phone,
        $to = null
    ) {
        try {
            return $this->prepareCall->prepare(
                $business,
                $phone,
                $to !== null ? $to : ucfirst($this->faker->phoneNumber)
            );
        } catch (InvalidDataException $e) {
            throw $e;
        } catch (InsufficientBalanceException $e) {
            throw $e;
        }
    }
}