<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Product\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectServices
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
     * @return Service[]
     */
    public function collect()
    {
        $services = $this->manageStorage->connect()->find();

        return iterator_to_array($services);
    }
}
