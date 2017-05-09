<?php

namespace Muchacuba\Aloleiro\Test;

use Faker\Factory;
use Faker\Generator;
use Muchacuba\Aloleiro\CollectBusinesses as BaseCollectBusinesses;
use Muchacuba\Aloleiro\Business;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickLastBusiness
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var BaseCollectBusinesses
     */
    private $collectBusinesses;

    /**
     * @param BaseCollectBusinesses $collectBusinesses
     */
    public function __construct(BaseCollectBusinesses $collectBusinesses)
    {
        $this->faker = Factory::create('es_ES');
        $this->collectBusinesses = $collectBusinesses;
    }

    /**
     * @return Business
     *
     * @throws \Exception
     */
    public function pick() {
        $businesses = $this->collectBusinesses->collect();

        if (empty($businesses)) {
            throw new \Exception();
        }

        return $businesses[count($businesses) - 1];
    }
}