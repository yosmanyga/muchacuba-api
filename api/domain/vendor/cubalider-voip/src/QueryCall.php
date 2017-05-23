<?php

namespace Cubalider\Voip;

use Cubalider\Voip\Sinch\QueryCall as QuerySinchCall;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class QueryCall
{
    /**
     * @var PickCall
     */
    private $pickCall;

    /**
     * @var QuerySinchCall
     */
    private $querySinchCall;

    /**
     * @var CompleteCall
     */
    private $completeCall;

    /**
     * @param PickCall       $pickCall
     * @param QuerySinchCall $querySinchCall
     * @param CompleteCall   $completeCall
     */
    public function __construct(
        PickCall $pickCall,
        QuerySinchCall $querySinchCall,
        CompleteCall $completeCall
    )
    {
        $this->pickCall = $pickCall;
        $this->querySinchCall = $querySinchCall;
        $this->completeCall = $completeCall;
    }

    /**
     * @param string $id
     *
     * @return array
     *
     * @throws NonExistentCallException
     * @throws \Exception
     */
    public function query($id)
    {
        try {
            $call = $this->pickCall->pick($id);
        } catch (NonExistentCallException $e) {
            throw $e;
        }
        
        if ($call->getProvider() == 'sinch') {
            $payload = $this->querySinchCall->query($call->getCid());
        } else {
            throw new \Exception();
        }

        $this->completeCall->complete(
            'sinch',
            $call->getCid(),
            strtotime($payload['timestamp']) - $payload['duration'],
            strtotime($payload['timestamp']),
            $payload['duration'],
            $payload['debit']['amount']
        );

        return $payload;
    }
}