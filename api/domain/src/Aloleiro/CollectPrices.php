<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Price\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectPrices
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param bool $favorites
     *
     * @return Price[]
     */
    public function collect($favorites = false)
    {
        $criteria = [];

        if ($favorites == true) {
            $criteria['favorite'] = true;
        }

        $prices = $this->manageStorage->connect()->find($criteria);

        return iterator_to_array($prices);
    }
}
