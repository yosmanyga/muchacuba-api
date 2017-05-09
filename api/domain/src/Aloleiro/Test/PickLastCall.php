<?php

namespace Muchacuba\Aloleiro\Test;

use Faker\Factory;
use Faker\Generator;
use Muchacuba\Aloleiro\Call;
use Muchacuba\Aloleiro\CollectCalls as BaseCollectCalls;
use Muchacuba\Aloleiro\Business;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickLastCall
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var BaseCollectCalls
     */
    private $collectCalls;

    /**
     * @param BaseCollectCalls $collectCalls
     */
    public function __construct(BaseCollectCalls $collectCalls)
    {
        $this->faker = Factory::create('es_ES');
        $this->collectCalls = $collectCalls;
    }

    /**
     * @param Business $business
     * 
     * @return Call
     *
     * @throws \Exception
     */
    public function pick(Business $business)
    {
        $calls = $this->collectCalls->collect($business);

        if (empty($calls)) {
            throw new \Exception();
        }

        return $calls[0];
    }
}