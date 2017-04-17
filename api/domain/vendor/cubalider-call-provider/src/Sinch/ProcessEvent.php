<?php

namespace Cubalider\Call\Provider\Sinch;

use Cubalider\Call\Provider\DelegateProcessEvent as BaseDelegateProcessEvent;
use Cubalider\Call\Provider\ProcessEvent as BaseProcessEvent;
use Cubalider\Call\Provider\RegisterLog;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessEvent implements BaseProcessEvent
{
    /**
     * @var BaseDelegateProcessEvent
     */
    private $delegateProcessEvent;

    /**
     * @var DelegateTranslateResponse
     */
    private $delegateTranslateResponse;

    /**
     * @var RegisterLog
     */
    private $registerLog;

    /**
     * @param ProcessEvent[]            $processEventServices
     * @param DelegateTranslateResponse $delegateTranslateResponse
     * @param RegisterLog               $registerLog
     *
     * @di\arguments({
     *     processEventServices: '#cubalider.call.provider.sinch.process_event'
     * })
     */
    public function __construct(
        array $processEventServices,
        DelegateTranslateResponse $delegateTranslateResponse,
        RegisterLog $registerLog
    )
    {
        $this->delegateProcessEvent = new BaseDelegateProcessEvent($processEventServices);
        $this->delegateTranslateResponse = $delegateTranslateResponse;
        $this->registerLog = $registerLog;
    }

    /**
     * {@inheritdoc}
     */
    public function process($payload)
    {
        $this->registerLog->register(
            'event-from-sinch',
            $payload
        );

        $response = $this->delegateProcessEvent->process($payload);

        return $this->delegateTranslateResponse->translate($response, $payload);
    }
}