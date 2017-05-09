<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Phone\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectPhones
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage       $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param Business|null $business
     *
     * @return Phone[]
     */
    public function collect(Business $business = null)
    {
        $criteria = [];

        if (!is_null($business)) {
            $criteria['business'] = $business->getId();
        }

        $phones = $this->manageStorage->connect()->find($criteria);

        return iterator_to_array($phones);
    }
}
