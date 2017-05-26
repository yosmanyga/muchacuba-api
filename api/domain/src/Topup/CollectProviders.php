<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Provider\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectProviders
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
     * @param string|null $country
     *
     * @return Provider[]
     */
    public function collect($country = null)
    {
        $criteria = [];

        if (!is_null($country)) {
            $criteria['country'] = $country;
        }

        $providers = $this->manageStorage->connect()->find($criteria);

        return iterator_to_array($providers);
    }
}
