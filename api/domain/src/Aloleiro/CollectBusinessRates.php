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
     * @var PickRate
     */
    private $pickRate;

    /**
     * @var CollectSystemRates
     */
    private $collectSystemRates;

    /**
     * @param PickProfile        $pickProfile
     * @param PickBusiness       $pickBusiness
     * @param PickRate           $pickRate
     * @param CollectSystemRates $collectSystemRates
     */
    public function __construct(
        PickProfile $pickProfile,
        PickBusiness $pickBusiness,
        PickRate $pickRate,
        CollectSystemRates $collectSystemRates
    )
    {
        $this->pickProfile = $pickProfile;
        $this->pickBusiness = $pickBusiness;
        $this->pickRate = $pickRate;
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

        $currencyExchange = $this->pickRate
            ->pick('Venezuela')
            ->getCountryCurrencyExchange();

        /** @var SystemRate[] $rates */
        $rates = $this->collectSystemRates->collect($favorite);

        $businessRates = [];
        foreach ($rates as $i => $rate) {
            // Currency exchange
            $purchase = $rate->getSale() * $currencyExchange;
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
