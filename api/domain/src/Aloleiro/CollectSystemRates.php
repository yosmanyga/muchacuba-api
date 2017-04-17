<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Rate\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectSystemRates
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var CollectCountries
     */
    private $collectCountries;

    /**
     * @var int
     */
    private $profitFactor;

    /**
     * @param ManageStorage    $manageStorage
     * @param CollectCountries $collectCountries
     * @param int              $profitFactor
     *
     * @di\arguments({
     *     profitFactor: "%profit_factor%"
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        CollectCountries $collectCountries,
        $profitFactor
    )
    {
        $this->manageStorage = $manageStorage;
        $this->collectCountries = $collectCountries;
        $this->profitFactor = $profitFactor;
    }

    /**
     * @param bool $favorite
     *
     * @return SystemRate[]
     */
    public function collect($favorite = false)
    {
        $favorites = [
            'Brasil',
            'Canada',
            'Chile',
            'China',
            'Colombia',
            'Costa Rica',
            'Cuba',
            'República Dominicana',
            'Ecuador',
            'El Salvador',
            'France',
            'Alemania',
            'Guatemala',
            'Honduras',
            'Italia',
            'Mexico',
            'Nicaragua',
            'Líbano',
            'Libia',
            'Paraguay',
            'Peru',
            'Puerto Rico',
            'España',
            'Estados Unidos',
            'Siria',
            'Uruguay'
        ];

        $countries = $this->collectCountries->collect();

        /** @var Rate[] $rates */
        $rates = $this->manageStorage->connect()->find();

        $systemRates = [];
        foreach ($rates as $rate) {
            $isFavorite = in_array($rate->getCountry(), $favorites);

            if ($favorite == true && $isFavorite == false) {
                continue;
            }

            $country = $this->translateCountry(
                $rate->getCountry(),
                $countries
            );
            $type = $this->translateType($rate->getType());

            // Purchase
            $sale = $rate->getValue();
            // Plus profit
            $sale += $sale * $this->profitFactor / 100;
            // Sale can't be less than 0.1
            $sale = max($sale, 0.1);
            // Round
            $sale = round($sale, 4);

            $systemRates[] = new SystemRate(
                $country,
                $type,
                $rate->getCode(),
                $isFavorite,
                $rate->getValue(),
                $sale
            );
        }

        return $systemRates;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function translateType($type)
    {
        return str_replace(
            ['Fixed', 'Mobile', 'Other'],
            ['Fijo', 'Móvil', 'Otro'],
            $type
        );
    }

    /**
     * @param string    $country
     * @param Country[] $translations
     *
     * @return string
     */
    private function translateCountry($country, $translations)
    {
        foreach ($translations as $translation) {
            if ($translation->getName() == $country) {
                return $translation->getTranslation();
            }
        }

        if ($country == 'Syrian Arab Republic') {
            return 'Siria';
        }

        return $country;
    }
}
