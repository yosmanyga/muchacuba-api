<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Approval\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectApprovals
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
     * @return Approval[]
     */
    public function collect()
    {
        $approvals = $this->manageStorage->connect()->find();

        return iterator_to_array($approvals);
    }
}
