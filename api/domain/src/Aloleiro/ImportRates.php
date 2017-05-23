<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Voip\Sinch\LoadRates;
use Muchacuba\Aloleiro\Rate\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportRates
{
    /**
     * @var LoadRates
     */
    private $loadRates;

    /**
     * @var PickCurrency
     */
    private $pickCurrency;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var AddRate
     */
    private $addRate;

    /**
     * @param LoadRates     $loadRates
     * @param PickCurrency  $pickCurrency
     * @param ManageStorage $manageStorage
     * @param AddRate       $addRate
     */
    public function __construct(
        LoadRates $loadRates,
        PickCurrency $pickCurrency,
        ManageStorage $manageStorage, 
        AddRate $addRate
    )
    {
        $this->loadRates = $loadRates;
        $this->pickCurrency = $pickCurrency;
        $this->manageStorage = $manageStorage;
        $this->addRate = $addRate;
    }

    /**
     */
    public function import()
    {
        $rates = $this->loadRates->load();

        $eurValue = $this->pickCurrency->pickEUR();

        // Purge to start from scratch
        $this->manageStorage->purge();

        foreach ($rates as $rate) {
            $this->addRate->add(
                $rate->getCountry(),
                $rate->getNetwork(),
                round($rate->getPrice() / $eurValue, 4)
            );
        }
    }
}