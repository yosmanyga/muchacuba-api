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
     * @var PickCurrency
     */
    private $pickCurrency;

    /**
     * @var CollectSystemRates
     */
    private $collectSystemRates;

    /**
     * @param PickCurrency       $pickCurrency
     * @param CollectSystemRates $collectSystemRates
     */
    public function __construct(
        PickCurrency $pickCurrency,
        CollectSystemRates $collectSystemRates
    )
    {
        $this->pickCurrency = $pickCurrency;
        $this->collectSystemRates = $collectSystemRates;
    }

    /**
     * @param Business $business
     * @param bool     $favorite
     *
     * @return BusinessRate[]
     */
    public function collect(Business $business, $favorite = false)
    {
        $currencyExchange = $this->pickCurrency
            ->pickVEF();

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
                $rate->getNetwork(),
                $rate->getPrefix(),
                $rate->isFavorite(),
                $purchase,
                $sale
            );
        }

        return $businessRates;
    }
}
