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
     * @return Price[]
     */
    public function collect()
    {
        $prices = $this->manageStorage->connect()->find();

        return iterator_to_array($prices);
    }
}
