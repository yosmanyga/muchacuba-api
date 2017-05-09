<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PrepareClientRates
{
    /**
     * @var PickCurrency
     */
    private $pickCurrency;

    /**
     * @var CollectClientRates
     */
    private $collectClientRates;

    /**
     * @param PickCurrency       $pickCurrency
     * @param CollectClientRates $collectClientRates
     */
    public function __construct(
        PickCurrency $pickCurrency,
        CollectClientRates $collectClientRates
    )
    {
        $this->pickCurrency = $pickCurrency;
        $this->collectClientRates = $collectClientRates;
    }

    /**
     * @param Business $business
     *
     * @return string
     */
    public function prepare(Business $business)
    {
        $currencyExchange = $this->pickCurrency
            ->pickVEF();

        $output = fopen(sprintf('%s/precios.csv', sys_get_temp_dir()), 'w');

        fputcsv($output, array('País', 'Red', 'Precio'));

        $rates = $this->collectClientRates->collect($business);
        foreach ($rates as $rate) {
            fputcsv($output, [
                $rate->getCountry(),
                $rate->getNetwork(),
                sprintf('%s Bf', round($rate->getSale() * $currencyExchange))
            ]);
        }

        return $output;
    }
}
