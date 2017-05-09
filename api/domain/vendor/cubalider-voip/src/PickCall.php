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
     * @param string $provider
     * @param string $cid
     *
     * @return Call
     *
     * @throws NonExistentCallException
     */
    public function pick($provider, $cid)
    {
        /** @var Call $call */
        $call = $this->manageStorage->connect()->findOne([
            'provider' => $provider,
            'cid' => $cid,
        ]);

        if (is_null($call)) {
            throw new NonExistentCallException();
        }

        return $call;
    }
}
