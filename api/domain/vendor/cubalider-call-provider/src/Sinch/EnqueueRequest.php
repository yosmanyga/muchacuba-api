<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\Sinch\Request\ManageStorage as ManageStorage;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class EnqueueRequest
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
     * @param string $cid
     */
    public function enqueue($cid)
    {
        $this->manageStorage->connect()->insertOne(new Request(
            $cid
        ));
    }
}