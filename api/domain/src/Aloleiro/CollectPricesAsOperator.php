<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectPricesAsOperator
{
    /**
     * @var CollectPricesAsSeller
     */
    private $collectPricesAsSeller;

    /**
     * @param CollectPricesAsSeller $collectPricesAsSeller
     */
    public function __construct(
        CollectPricesAsSeller $collectPricesAsSeller
    )
    {
        $this->collectPricesAsSeller = $collectPricesAsSeller;
    }

    /**
     * @param string $uniqueness
     * @param bool   $favorites
     *
     * @return PriceAsOperator[]
     */
    public function collect($uniqueness, $favorites = false)
    {
        /** @var PriceAsSeller[] $prices */
        $prices = $this->collectPricesAsSeller->collect($uniqueness, $favorites);

        $pricesAsOperator = [];
        foreach ($prices as $i => $price) {
            $pricesAsOperator[] = new PriceAsOperator(
                $price->getId(),
                $price->getCountry(),
                $price->getPrefix(),
                $price->getCode(),
                $price->getType(),
                $price->isFavorite(),
                $price->getSaleValue()
            );
        }

        return $pricesAsOperator;
    }
}
