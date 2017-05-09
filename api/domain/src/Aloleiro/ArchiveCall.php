<?php

namespace Muchacuba\Aloleiro;

use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ArchiveCall
{
    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(
        ManageCallStorage $manageCallStorage
    )
    {
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * @param Business $business
     * @param string   $id
     *
     * @throws NonExistentCallException
     */
    public function archive(Business $business, $id)
    {
        /** @var UpdateResult $result */
        $result = $this->manageCallStorage->connect()->updateOne(
            [
                '_id' => $id,
                'business' =>$business->getId()
            ],
            [
                '$set' => ['status' => Call::STATUS_ARCHIVED]
            ]);

        if ($result->getModifiedCount() === 0) {
            throw new NonExistentCallException();
        }
    }
}