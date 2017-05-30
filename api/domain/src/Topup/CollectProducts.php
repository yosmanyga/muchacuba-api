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
     * @param Provider $provider
     *
     * @return Product[]
     */
    public function collect(Provider $provider = null)
    {
        $criteria = [];

        if(!is_null($provider)) {
            $criteria['provider'] = $provider->getId();
        }

        $products = $this->manageStorage->connect()->find($criteria);

        return iterator_to_array($products);
    }
}
