<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectBusinessRates
{
    /**
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var PickBusiness
     */
    private $pickBusiness;

    /**
     * @var PickCountry
     */
    private $pickCountry;

    /**
     * @var CollectSystemRates
     */
    private $collectSystemRates;

    /**
     * @param PickProfile        $pickProfile
     * @param PickBusiness       $pickBusiness
     * @param PickCountry        $pickCountry
     * @param CollectSystemRates $collectSystemRates
     */
    public function __construct(
        PickProfile $pickProfile,
        PickBusiness $pickBusiness,
        PickCountry $pickCountry,
        CollectSystemRates $collectSystemRates
    )
    {
        $this->pickProfile = $pickProfile;
        $this->pickBusiness = $pickBusiness;
        $this->pickCountry = $pickCountry;
        $this->collectSystemRates = $collectSystemRates;
    }

    /**
     * @param string $uniqueness
     * @param bool   $favorite
     *
     * @return BusinessRate[]
     */
    public function collect($uniqueness, $favorite = false)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        $business = $this->pickBusiness->pick($profile->getBusiness());

        $country = $this->pickCountry->pick('Venezuela');

        /** @var SystemRate[] $rates */
        $rates = $this->collectSystemRates->collect($favorite);

        $businessRates = [];
        foreach ($rates as $i => $rate) {
            // Currency exchange
            $purchase = $rate->getSale() * $country->getCurrencyExchange();
            // Round
            $purchase = round($purchase);

            // Purchase
            $sale = $purchase;
            // Plus profit
            $sale += $purchase * $business->getProfitPercent() / 100;
            // Round
            $sale = round($sale);

            $businessRates[] = new BusinessRate(
                $rate->getCountry(),
                $rate->getType(),
                $rate->getCode(),
                $rate->isFavorite(),
                $purchase,
                $sale
            );
        }

        return $businessRates;
    }
}
