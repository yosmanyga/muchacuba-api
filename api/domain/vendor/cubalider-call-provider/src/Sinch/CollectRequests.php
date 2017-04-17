<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\Sinch\Request\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectRequests
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
     * @return Request[]
     */
    public function collect()
    {
        $requests = $this->manageStorage->connect()->find();

        return iterator_to_array($requests);
    }
}
