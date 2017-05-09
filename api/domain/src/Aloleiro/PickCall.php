<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Call\ManageStorage;

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
    ) {
        $this->manageStorage = $manageStorage;
    }

    /**
     * @param string $from
     *
     * @return Call
     *
     * @throws NonExistentCallException
     */
    public function pick($from)
    {
        /** @var Call $call */
        $call = $this->manageStorage->connect()
            ->findOne([
                'from' => $from
            ]);

        if (is_null($call)) {
            throw new NonExistentCallException();
        }

        return $call;
    }
}
