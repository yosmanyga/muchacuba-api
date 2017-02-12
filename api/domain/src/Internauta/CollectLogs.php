<?php

namespace Muchacuba\Internauta;

use Muchacuba\Internauta\Log\ManageStorage;

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
    public function __construct(ManageStorage $manageStorage)
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @return Log[]
     */
    public function collect()
    {
        return iterator_to_array($this->manageStorage->connect()->find());
    }
}