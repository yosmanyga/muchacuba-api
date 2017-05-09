<?php

namespace Muchacuba\Aloleiro\Call;

use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageInstanceStorage;
use Muchacuba\Aloleiro\NonExistentCallException;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class MarkInstance
{
    /**
     * @var ManageInstanceStorage
     */
    private $manageInstanceStorage;

    /**
     * @param ManageInstanceStorage $manageInstanceStorage
     */
    public function __construct(
        ManageInstanceStorage $manageInstanceStorage
    )
    {
        $this->manageInstanceStorage = $manageInstanceStorage;
    }

    /**
     * @param Business $business
     * @param string   $call
     * @param string   $id
     *
     * @throws NonExistentCallException
     */
    public function markAsDidSpeak(Business $business, $call, $id)
    {
        try {
            $this->mark($business, $call, $id, Instance::RESULT_DID_SPEAK);
        } catch (NonExistentCallException $e) {
            throw $e;
        }
    }

    /**
     * @param Business $business
     * @param string   $call
     * @param string   $id
     *
     * @throws NonExistentCallException
     */
    public function markAsDidNotSpeak(Business $business, $call, $id)
    {
        try {
            $this->mark($business, $call, $id, Instance::RESULT_DID_NOT_SPEAK);
        } catch (NonExistentCallException $e) {
            throw $e;
        }
    }

    /**
     * @param Business $business
     * @param string   $call
     * @param string   $id
     * @param int      $result
     *
     * @throws NonExistentCallException
     */
    private function mark(Business $business, $call, $id, $result)
    {
        /** @var UpdateResult $result */
        $result = $this->manageInstanceStorage->connect()->updateOne(
            [
                '_id' => $call,
                'business' =>$business->getId(),
                'instances.id' => $id
            ],
            ['$set' => [
                'instances.$.result' => $result
            ]]);

        if ($result->getModifiedCount() === 0) {
            throw new NonExistentCallException();
        }
    }
}