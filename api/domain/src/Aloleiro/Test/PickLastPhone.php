<?php

namespace Muchacuba\Aloleiro\Test;

use Faker\Factory;
use Faker\Generator;
use Muchacuba\Aloleiro\CollectPhones as BaseCollectPhones;
use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\Phone;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickLastPhone
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var BaseCollectPhones
     */
    private $collectPhones;

    /**
     * @param BaseCollectPhones $collectPhones
     */
    public function __construct(BaseCollectPhones $collectPhones)
    {
        $this->faker = Factory::create('es_ES');
        $this->collectPhones = $collectPhones;
    }

    /**
     * @param Business|null $business
     *
     * @return Phone
     *
     * @throws \Exception
     */
    public function pick(
        Business $business = null
    ) {
        $phones = $this->collectPhones->collect($business);

        if (empty($phones)) {
            throw new \Exception();
        }

        return $phones[count($phones) - 1];
    }
}