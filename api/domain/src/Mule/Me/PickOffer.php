<?php

namespace Muchacuba\Mule\Me;

use Muchacuba\Mule\NonExistentOfferException;
use Muchacuba\Mule\NonExistentProfileException;
use Muchacuba\Mule\Offer;
use Muchacuba\Mule\PickProfile;
use Muchacuba\Mule\PickOffer as BasePickOffer;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickOffer
{
    /**
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var BasePickOffer
     */
    private $pickOffer;

    /**
     * @param PickProfile   $pickProfile
     * @param BasePickOffer $pickOffer
     */
    public function __construct(
        PickProfile $pickProfile,
        BasePickOffer $pickOffer
    ) {
        $this->pickProfile = $pickProfile;
        $this->pickOffer = $pickOffer;
    }

    /**
     * @param string $uniqueness
     *
     * @return Offer
     *
     * @throws NonExistentProfileException
     * @throws NonExistentOfferException
     */
    public function pick($uniqueness)
    {
        try {
            $profile = $this->pickProfile->pick($uniqueness);
        } catch (NonExistentProfileException $e) {
            throw $e;
        }

        try {
            $offer = $this->pickOffer->pick($profile->getOffer());
        } catch (NonExistentOfferException $e) {
            throw $e;
        }

        return $offer;
    }
}
