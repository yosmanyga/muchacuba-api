<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Call\Provider\ContinueResponse;
use Cubalider\Call\Provider\ListenAnswerCallEvent as BaseListenAnswerCallEvent;
use MongoDB\BSON\UTCDateTime;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;

/**
 * @di\service({
 *     deductible: true,
 *     tags: [{
 *          name: 'cubalider.call.provider.listen_answer_call_event',
 *          key: 'aloleiro.business'
 *     }]
 * })
 */
class ListenAnswerCallEvent implements BaseListenAnswerCallEvent
{
    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(ManageCallStorage $manageCallStorage)
    {
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($cid, $timestamp)
    {
        $this->manageCallStorage->connect()->updateOne(
            ['instances.id' => $cid],
            ['$set' => ['instances.$.start' => new UTCDateTime($timestamp * 1000)]]
        );

        return new ContinueResponse();
    }
}