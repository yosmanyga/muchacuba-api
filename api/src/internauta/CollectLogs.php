<?php

namespace Muchacuba\Internauta;

use Muchacuba\Internauta\Log\ManageStorage;

/**
 * @di\service()
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
     * @http\resolution({method: "GET", path: "/internauta/collect-logs"})
     *
     * @return Logs
     */
    public function collect()
    {
        return new Logs($this->manageStorage->connect()->find([], ['limit' => 100]));
    }
}