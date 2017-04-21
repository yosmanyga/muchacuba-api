<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Business\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectBusinesses
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
     * @return Business[]
     */
    public function collect()
    {
        $businesses = $this->manageStorage->connect()->find();

        return iterator_to_array($businesses);
    }
}
