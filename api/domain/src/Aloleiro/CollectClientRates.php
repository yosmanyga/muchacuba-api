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
     * @param string $uniqueness
     * @param bool   $favorite
     *
     * @return ClientRate[]
     */
    public function collect($uniqueness, $favorite = false)
    {
        /** @var BusinessRate[] $businessRates */
        $businessRates = $this->collectBusinessRates->collect($uniqueness, $favorite);

        $clientRates = [];
        foreach ($businessRates as $i => $businessRate) {
            $clientRates[] = new ClientRate(
                $businessRate->getCountry(),
                $businessRate->getType(),
                $businessRate->getCode(),
                $businessRate->isFavorite(),
                $businessRate->getSale()
            );
        }

        return $clientRates;
    }
}
