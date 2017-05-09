<?php

namespace Cubalider\Voip;

use Cubalider\Voip\Log\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectLogs
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
     * @return Log[]
     */
    public function collect()
    {
        $logs = $this->manageStorage->connect()->find();

        return iterator_to_array($logs);
    }
}
