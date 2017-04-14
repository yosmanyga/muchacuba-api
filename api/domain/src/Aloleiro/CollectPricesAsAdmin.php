<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Price\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectPricesAsAdmin
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var int
     */
    private $profitFactor;

    /**
     * @param ManageStorage $manageStorage
     * @param int           $profitFactor
     *
     * @di\arguments({
     *     profitFactor: "%admin_profit_factor%"
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        $profitFactor
    )
    {
        $this->manageStorage = $manageStorage;
        $this->profitFactor = $profitFactor;
    }

    /**
     * @param bool $favorites
     *
     * @return PriceAsAdmin[]
     */
    public function collect($favorites = false)
    {
        $criteria = [];

        if ($favorites == true) {
            $criteria['favorite'] = true;
        }

        /** @var Price[] $prices */
        $prices = $this->manageStorage->connect()->find($criteria);

        $pricesAsAdmin = [];
        foreach ($prices as $price) {
            $saleValue =
                // Purchase
                $price->getValue()
                // + profit
                + $price->getValue() * $this->profitFactor / 100;
            // Sale value can't be less than 0.1
            $saleValue = max($saleValue, 0.1);
            // Round
            $saleValue = round($saleValue, 4);

            $pricesAsAdmin[] = new PriceAsAdmin(
                $price->getId(),
                $price->getCountry(),
                $price->getPrefix(),
                $price->getCode(),
                $price->getType(),
                $price->isFavorite(),
                $price->getValue(),
                $saleValue
            );
        }

        return $pricesAsAdmin;
    }
}
