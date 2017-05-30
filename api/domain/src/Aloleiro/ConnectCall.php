<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Phone\InvalidNumberException;
use Cubalider\Voip\ConnectResponse;
use Cubalider\Voip\HangupResponse;
use Cubalider\Voip\ListenIncomingEvent;
use MongoDB\BSON\UTCDateTime;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Call\Instance;
use Cubalider\Phone\FixNumber;

/**
 * @di\service({
 *     deductible: true,
 *     tags: ['cubalider.voip.listen_incoming_event']
 * })
 */
class ConnectCall implements ListenIncomingEvent
{
    /**
     * @var FixNumber
     */
    private $fixNumber;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param FixNumber         $fixNumber
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(
        FixNumber $fixNumber,
        ManageCallStorage $manageCallStorage
    )
    {
        $this->fixNumber = $fixNumber;
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($from, $id)
    {
        try {
            $from = $this->fixNumber->fix($from);
        } catch (InvalidNumberException $e) {
            return new HangupResponse("Número incorrecto");
        }

        /** @var Call $call */
        $call = $this->manageCallStorage->connect()->findOne(
            [
                'status' => Call::STATUS_ACTIVE,
                'from' => $from
            ],
            [
                'sort' => [
                    // Last prepared call
                    '_id' => -1
                ]
            ]
        );

        if (is_null($call)) {
            return new HangupResponse("Llamada no autorizada");
        }

        $this->manageCallStorage->connect()->updateOne(
            ['_id' => $call->getId()],
            ['$push' => ['instances' => new Instance(
                $id,
                // This start time is temporal. It will be replaced by the real
                // one when the call is completed
                new UTCDateTime(time() * 1000)
            )]]
        );

        return new ConnectResponse($call->getTo());
    }
}