<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Promotion\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectPromotions
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
     * @return Promotion[]
     */
    public function collect()
    {
        $promotions = $this->manageStorage->connect()->find();

        return iterator_to_array($promotions);
    }
}
