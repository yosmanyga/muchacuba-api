<?php

namespace Cubalider\Voip;

use Cubalider\Voip\Call\ManageStorage;
use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\NonExistentCallException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdateCall
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
     * @param string $id
     * @param float  $cost
     *
     * @return Call
     *
     * @throws NonExistentCallException
     */
    public function updateWithOutbound($id, $cost)
    {
        /** @var Call $call */
        $call = $this->manageStorage->connect()->findOne([
            '_id' => $id
        ]);

        if (is_null($call)) {
            throw new NonExistentCallException();
        }

        $this->manageStorage->connect()->updateOne(
            ['_id' => $id],
            [
                '$inc' => [
                    'cost' => $cost
                ],
                '$set' => [
                    'status' => $call->getStatus() == Call::STATUS_NONE
                        ? Call::STATUS_FIRST
                        : Call::STATUS_SECOND
                ]
            ]
        );

        /** @var Call $call */
        $call = $this->manageStorage->connect()->findOne([
            '_id' => $id
        ]);

        return $call;
    }

    /**
     * @param string $id
     * @param float  $cost
     * @param int    $start
     * @param int    $end
     * @param int    $duration
     *
     * @return Call
     *
     * @throws NonExistentCallException
     */
    public function updateWithInbound($id, $cost, $start, $end, $duration)
    {
        /** @var Call $call */
        $call = $this->manageStorage->connect()->findOne([
            '_id' => $id
        ]);

        if (is_null($call)) {
            throw new NonExistentCallException();
        }

        $this->manageStorage->connect()->updateOne(
            ['_id' => $id],
            [
                '$inc' => [
                    'cost' => $cost
                ],
                '$set' => [
                    'status' => $call->getStatus() == Call::STATUS_NONE
                        ? Call::STATUS_FIRST
                        : Call::STATUS_SECOND
                ]
            ]
        );

        $this->manageStorage->connect()->updateOne(
            ['_id' => $id],
            [
                '$set' => [
                    'duration' => $duration,
                    'start' => $start,
                    'end' => $end
                ]
            ]
        );

        /** @var Call $call */
        $call = $this->manageStorage->connect()->findOne([
            '_id' => $id
        ]);

        return $call;
    }
}
