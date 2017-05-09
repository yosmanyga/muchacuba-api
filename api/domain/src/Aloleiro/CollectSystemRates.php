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
     * @var int
     */
    private $profitPercent;

    /**
     * @param ManageStorage $manageStorage
     * @param int           $profitPercent
     *
     * @di\arguments({
     *     profitPercent: "%profit_percent%"
     * })
     */
    public function __construct(
        ManageStorage $manageStorage,
        $profitPercent
    )
    {
        $this->manageStorage = $manageStorage;
        $this->profitPercent = $profitPercent;
    }

    /**
     * @param bool $favorite
     *
     * @return SystemRate[]
     */
    public function collect($favorite = false)
    {
        $favorites = [
            'Alemania',
            'Brasil',
            'Canada',
            'Chile',
            'China',
            'Colombia',
            'Costa Rica',
            'Cuba',
            'Ecuador',
            'El Salvador',
            'España',
            'Estados Unidos',
            'Francia',
            'Guatemala',
            'Honduras',
            'Italia',
            'México',
            'Nicaragua',
            'Libano',
            'Paraguay',
            'Perú',
            'Puerto Rico',
            'República Dominicana',
            'Siria',
            'Uruguay'
        ];

        /** @var Rate[] $rates */
        $rates = $this->manageStorage->connect()->find();

        $systemRates = [];
        foreach ($rates as $rate) {
            $isFavorite = in_array($rate->getCountryTranslation(), $favorites);

            if ($favorite == true && $isFavorite == false) {
                continue;
            }

            // Purchase
            $sale = $rate->getPrice();
            // Plus profit
            $sale += $sale * $this->profitPercent / 100;
            // Round
            $sale = round($sale, 4);

            $systemRates[] = new SystemRate(
                $rate->getCountryTranslation(),
                $rate->getNetwork(),
                $rate->getPrefix(),
                $isFavorite,
                $rate->getPrice(),
                $sale
            );
        }

        // Sort after the translation
        usort($systemRates, function(SystemRate $a, SystemRate $b) {
            if ($a->getCountry() == $b->getCountry()) {
                return 0;
            }
            return ($a->getCountry() < $b->getCountry()) ? -1 : 1;
        });

        return $systemRates;
    }
}
