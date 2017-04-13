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
     * @var RegisterLog
     */
    private $registerLog;

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
     * @param RegisterLog   $registerLog
     * @param ProcessICEvent  $processICEvent
     * @param ProcessACEvent  $processACEvent
     * @param ProcessDICEvent $processDICEvent
     */
    public function __construct(
        RegisterLog $registerLog,
        ProcessICEvent $processICEvent,
        ProcessACEvent $processACEvent,
        ProcessDICEvent $processDICEvent
    )
    {
        $this->registerLog = $registerLog;
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
        $this->registerLog->register('sinch-event', $payload);

        $response = null;

        try {
            switch ($payload['event']) {
                case 'ice':
                    $response = $this->processICEvent->process(
                        $payload['cli'],
                        $payload['callid'],
                        $payload['to']['endpoint']
                    );

                    break;
                case 'ace':
                    $response = $this->processACEvent->process(
                        $payload['callid']
                    );

                    break;
                case 'dice':
                    $this->processDICEvent->process(
                        $payload['callid']
                    );

                    break;
                default:
                    throw new \Exception(sprintf("Event '%s' not supported", $payload['event']));
            }
        } catch (\Exception $e) {
            $this->registerLog->register('exception', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return null;
        }

        if ($response != null) {
            $this->registerLog->register(
                'response-to-sinch',
                $response
            );
        }

        return $response;
    }
}