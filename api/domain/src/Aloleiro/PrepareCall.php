<?php

namespace Muchacuba\Aloleiro;

use MongoDB\Driver\Exception\BulkWriteException;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Profile\ManageStorage as ManageProfileStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PrepareCall
{
    /**
     * @var ManageProfileStorage
     */
    private $manageProfileStorage;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param ManageProfileStorage $manageProfileStorage
     * @param ManageCallStorage    $manageCallStorage
     */
    public function __construct(
        ManageProfileStorage $manageProfileStorage,
        ManageCallStorage $manageCallStorage
    )
    {
        $this->manageProfileStorage = $manageProfileStorage;
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * @param string $uniqueness
     * @param string $from
     * @param string $to
     */
    public function prepare($uniqueness, $from, $to)
    {
        /** @var Profile $profile */
        $profile = $this->manageProfileStorage->connect()->findOne([
            '_id' => $uniqueness,
            'phones' => ['$in' => [$from]]
        ]);
    
        if (is_null($profile)) {
            // TODO
        }

        $id = uniqid();

        $this->manageCallStorage->connect()->insertOne(new Call(
            $id,
            $uniqueness,
            null,
            $from,
            $to,
            Call::STATUS_PREPARED
        ));
    }
}