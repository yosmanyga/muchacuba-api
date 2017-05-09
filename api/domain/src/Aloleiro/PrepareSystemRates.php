<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PrepareSystemRates
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
     * @param bool $favorites
     *
     * @return string
     */
    public function prepare($favorites = false)
    {
        $currencyExchange = $this->pickCurrency
            ->pickVEF();

        $output = fopen(sprintf('%s/precios.csv', sys_get_temp_dir()), 'w');

        fputcsv($output, array('País', 'Red', 'Precio'));

        $rates = $this->collectSystemRates->collect($favorites);
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
