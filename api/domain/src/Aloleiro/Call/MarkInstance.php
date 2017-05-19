<?php

namespace Muchacuba\Aloleiro\Call;

use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Business;
use Muchacuba\Aloleiro\Call;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageInstanceStorage;
use Muchacuba\Aloleiro\NonExistentCallException;
use Muchacuba\Aloleiro\Business\ManageBalance;

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
     * @var ManageBalance
     */
    private $manageBalance;

    /**
     * @param ManageInstanceStorage $manageInstanceStorage
     * @param ManageBalance         $manageBalance
     */
    public function __construct(
        ManageInstanceStorage $manageInstanceStorage,
        ManageBalance $manageBalance
    )
    {
        $this->manageInstanceStorage = $manageInstanceStorage;
        $this->manageBalance = $manageBalance;
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
            $businessPurchase = $this->mark(
                $business,
                $call,
                $id,
                Instance::RESULT_DID_SPEAK
            );
        } catch (NonExistentCallException $e) {
            throw $e;
        } catch (AlreadyMarkedInstanceException $e) {
            return;
        }

        $this->manageBalance->increase($business, $businessPurchase);
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
            $businessPurchase = $this->mark(
                $business, 
                $call, 
                $id, 
                Instance::RESULT_DID_NOT_SPEAK
            );
        } catch (NonExistentCallException $e) {
            throw $e;
        } catch (AlreadyMarkedInstanceException $e) {
            return;
        }

        $this->manageBalance->decrease($business, $businessPurchase);
    }

    /**
     * @param Business $business
     * @param string   $call
     * @param string   $id
     * @param int      $result
     *
     * @return float The business purchase
     *
     * @throws NonExistentCallException
     * @throws AlreadyMarkedInstanceException
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

        if ($result->getMatchedCount() === 0) {
            throw new NonExistentCallException();
        }

        if ($result->getModifiedCount() === 0) {
            throw new AlreadyMarkedInstanceException();
        }

        /** @var Call $call */
        $call = $this->manageInstanceStorage->connect()->findOne([
            '_id' => $call,
            'business' =>$business->getId(),
        ]);

        foreach ($call->getInstances() as $instance) {
            if ($instance['id'] == $id) {
                return $instance['businessPurchase'];
            }
        }

        throw new NonExistentCallException();
    }
}