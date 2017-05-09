<?php

namespace Muchacuba\Aloleiro\Test;

use Faker\Factory;
use Faker\Generator;
use Muchacuba\Aloleiro\AddBusiness as BaseAddBusiness;
use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\Business\InvalidDataException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AddBusiness
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var BaseAddBusiness
     */
    private $addBusiness;

    /**
     * @param BaseAddBusiness $addBusiness
     */
    public function __construct(BaseAddBusiness $addBusiness)
    {
        $this->faker = Factory::create('es_ES');
        $this->addBusiness = $addBusiness;
    }

    /**
     * @param int|null    $profitPercent
     * @param float|null  $balance
     * @param string|null $name
     * @param string|null $address
     *
     * @return Business
     *
     * @throws InvalidDataException
     */
    public function add(
        $profitPercent = null,
        $balance = null,
        $name = null,
        $address = null
    ) {
        try {
            return $this->addBusiness->add(
                $profitPercent !== null ? $profitPercent : rand(1, 50),
                $balance !== null ? $balance : rand(1, 50000),
                $name !== null ? $name : ucfirst($this->faker->word),
                $address !== null ? $name : $this->faker->address
            );
        } catch (InvalidDataException $e) {
            throw $e;
        }
    }
}