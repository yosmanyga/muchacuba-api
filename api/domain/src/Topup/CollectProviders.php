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
     * @return Provider[]
     */
    public function collect()
    {
        $providers = $this->manageStorage->connect()->find();

        return iterator_to_array($providers);
    }
}
