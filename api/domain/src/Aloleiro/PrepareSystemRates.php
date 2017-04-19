<?php

namespace Muchacuba\Aloleiro;

use Dompdf\Dompdf;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PrepareSystemRates
{
    /**
     * @var CollectSystemRates
     */
    private $collectSystemRates;

    /**
     * @var PickRate
     */
    private $pickRate;

    /**
     * @param CollectSystemRates $collectSystemRates
     * @param PickRate        $pickRate
     */
    public function __construct(
        CollectSystemRates $collectSystemRates,
        PickRate $pickRate
    )
    {
        $this->collectSystemRates = $collectSystemRates;
        $this->pickRate = $pickRate;
    }

    /**
     * @param bool $favorites
     *
     * @return string
     */
    public function prepare($favorites = false)
    {
        $currencyExchange = $this->pickRate
            ->pick('Venezuela')
            ->getCountryCurrencyExchange();

        $output = fopen(sprintf('%s/precios.csv', sys_get_temp_dir()), 'w');

        fputcsv($output, array('País', 'Tipo', 'Precio'));

        $rates = $this->collectSystemRates->collect($favorites);
        foreach ($rates as $rate) {
            fputcsv($output, [
                $rate->getCountry(),
                $rate->getType(),
                sprintf('%s Bf', round($rate->getSale() * $currencyExchange))
            ]);
        }

        return $output;
    }
}
