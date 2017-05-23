<?php

namespace Cubalider\Voip;

use Cubalider\Voip\Call\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickCall
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
     * @param string|null $id
     * @param string|null $provider
     * @param string|null $cid
     *
     * @return Call
     *
     * @throws NonExistentCallException
     */
    public function pick($id = null, $provider = null, $cid = null)
    {
        $criteria = [];

        if (!is_null($id)) {
            $criteria['_id'] = $id;
        }

        if (!is_null($provider)) {
            $criteria['provider'] = $provider;
        }

        if (!is_null($cid)) {
            $criteria['cid'] = $cid;
        }

        /** @var Call $call */
        $call = $this->manageStorage->connect()->findOne($criteria);

        if (is_null($call)) {
            throw new NonExistentCallException();
        }

        return $call;
    }
}
