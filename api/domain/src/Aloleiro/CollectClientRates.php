<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectClientRates
{
    /**
     * @var CollectBusinessRates
     */
    public $collectBusinessRates;

    /**
     * @param CollectBusinessRates $collectBusinessRates
     */
    public function __construct(
        CollectBusinessRates $collectBusinessRates
    )
    {
        $this->collectBusinessRates = $collectBusinessRates;
    }

    /**
     * @param Business $business
     * @param bool     $favorite
     *
     * @return ClientRate[]
     */
    public function collect(Business $business, $favorite = false)
    {
        /** @var BusinessRate[] $businessRates */
        $businessRates = $this->collectBusinessRates->collect($business, $favorite);

        $clientRates = [];
        foreach ($businessRates as $i => $businessRate) {
            $clientRates[] = new ClientRate(
                $businessRate->getCountry(),
                $businessRate->getNetwork(),
                $businessRate->getPrefix(),
                $businessRate->isFavorite(),
                $businessRate->getSale()
            );
        }

        return $clientRates;
    }
}
