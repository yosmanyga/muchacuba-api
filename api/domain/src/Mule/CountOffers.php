<?php

namespace Muchacuba\Mule;

use Muchacuba\Mule\Offer\ConnectToStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CountOffers
{
    /**
     * @var ConnectToStorage
     */
    private $connectToStorage;

    /**
     * @param ConnectToStorage $connectToStorage
     */
    public function __construct(
        ConnectToStorage $connectToStorage
    ) {
        $this->connectToStorage = $connectToStorage;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->connectToStorage->connect()
            ->count();
    }
}
