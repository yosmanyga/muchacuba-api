<?php

namespace Muchacuba\Aloleiro;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ProcessEvent
{
    /**
     * @var RegisterEvent
     */
    private $registerEvent;

    /**
     * @var ProcessICEvent
     */
    private $processICEvent;

    /**
     * @var ProcessACEvent
     */
    private $processACEvent;

    /**
     * @var ProcessDICEvent
     */
    private $processDICEvent;

    /**
     * @param RegisterEvent   $registerEvent
     * @param ProcessICEvent  $processICEvent
     * @param ProcessACEvent  $processACEvent
     * @param ProcessDICEvent $processDICEvent
     */
    public function __construct(
        RegisterEvent $registerEvent,
        ProcessICEvent $processICEvent,
        ProcessACEvent $processACEvent,
        ProcessDICEvent $processDICEvent
    )
    {
        $this->registerEvent = $registerEvent;
        $this->processICEvent = $processICEvent;
        $this->processACEvent = $processACEvent;
        $this->processDICEvent = $processDICEvent;
    }

    /**
     * @param array $payload
     *
     * @return string|null
     */
    public function process($payload)
    {
        $this->registerEvent->register(
            $payload
        );

        switch ($payload['event']) {
            case 'ice':
                $response = $this->processICEvent->process(
                    $payload['cli'],
                    $payload['callid']
                );

                break;
            case 'ace':
                $response = $this->processACEvent->process(
                    $payload['callid']
                );

                break;
            case 'dice':
                $response = $this->processDICEvent->process(
                    $payload['callid']
                );

                break;
            default:
                $response = [];
        }

        $this->registerEvent->register(
            $response
        );

        return $response;
    }
}