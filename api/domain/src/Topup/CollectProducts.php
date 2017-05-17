<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Product\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectProducts
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
     * @return Product[]
     */
    public function collect()
    {
        $products = $this->manageStorage->connect()->find();

        return iterator_to_array($products);
    }
}
