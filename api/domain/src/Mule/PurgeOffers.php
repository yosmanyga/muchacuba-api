<?php

namespace Muchacuba\Mule;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PurgeOffers
{
    /**
     * @var InitOffer
     */
    private $initOffer;

    /**
     * @param InitOffer $initOffer
     */
    public function __construct(
        InitOffer $initOffer
    ) {
        $this->initOffer = $initOffer;
    }

    /**
     */
    public function purge()
    {
        $this->initOffer->init()->clearIndex();
    }
}
