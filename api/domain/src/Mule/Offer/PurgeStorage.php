<?php

namespace Muchacuba\Mule\Offer;

use Muchacuba\Mule\InitOffer;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class PurgeStorage
{
    /**
     * @var ConnectToStorage
     */
    private $connectToStorage;

    /**
     * @var InitOffer
     */
    private $initOffer;

    /**
     * @param ConnectToStorage $connectToStorage
     * @param InitOffer        $initOffer
     */
    public function __construct(
        ConnectToStorage $connectToStorage,
        InitOffer $initOffer
    ) {
        $this->connectToStorage = $connectToStorage;
        $this->initOffer = $initOffer;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $this->initOffer->init()->clearIndex();
        $this->connectToStorage->connect()->drop();
    }
}
