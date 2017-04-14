<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectPricesAsSeller
{
    /**
     * @var PickProfile
     */
    private $pickProfile;

    /**
     * @var CollectPricesAsAdmin
     */
    private $collectPricesAsAdmin;

    /**
     * @var float
     */
    private $currencyExchange;

    /**
     * @param PickProfile          $pickProfile
     * @param CollectPricesAsAdmin $collectPricesAsAdmin
     * @param float                $currencyExchange
     *
     * @di\arguments({
     *     currencyExchange: "%currency_exchange%"
     * })
     */
    public function __construct(
        PickProfile $pickProfile,
        CollectPricesAsAdmin $collectPricesAsAdmin,
        $currencyExchange
    )
    {
        $this->pickProfile = $pickProfile;
        $this->collectPricesAsAdmin = $collectPricesAsAdmin;
        $this->currencyExchange = $currencyExchange;
    }

    /**
     * @param string $uniqueness
     * @param bool   $favorites
     *
     * @return PriceAsSeller[]
     *
     * @throws NonExistentProfileException
     */
    public function collect($uniqueness, $favorites = false)
    {
        $profile = $this->pickProfile->pick($uniqueness);

        if (is_null($profile)) {
            throw new NonExistentProfileException();
        }

        /** @var PriceAsAdmin[] $prices */
        $prices = $this->collectPricesAsAdmin->collect($favorites);

        $pricesAsSeller = [];
        foreach ($prices as $i => $price) {
            // Currency exchange
            $purchaseValue = $price->getSaleValue() * $this->currencyExchange;
            // Round
            $purchaseValue = round($purchaseValue);

            // Purchase
            $saleValue = $purchaseValue;
            // + profit
            $saleValue += $purchaseValue * $profile->getProfitFactor() / 100;
            // Round
            $saleValue = round($saleValue);

            $pricesAsSeller[] = new PriceAsSeller(
                $price->getId(),
                $price->getCountry(),
                $price->getPrefix(),
                $price->getCode(),
                $price->getType(),
                $price->isFavorite(),
                $purchaseValue,
                $saleValue
            );
        }

        return $pricesAsSeller;
    }
}
