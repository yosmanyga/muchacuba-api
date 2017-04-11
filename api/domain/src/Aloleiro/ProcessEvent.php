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
     * @var ProcessICEEvent
     */
    private $processICEEvent;

    /**
     * @var ProcessACEEvent
     */
    private $processACEEvent;

    /**
     * @var ProcessDICEEvent
     */
    private $processDICEEvent;

    /**
     * @param RegisterEvent    $registerEvent
     * @param ProcessICEEvent  $processICEEvent
     * @param ProcessACEEvent  $processACEEvent
     * @param ProcessDICEEvent $processDICEEvent
     */
    public function __construct(
        RegisterEvent $registerEvent,
        ProcessICEEvent $processICEEvent,
        ProcessACEEvent $processACEEvent,
        ProcessDICEEvent $processDICEEvent
    )
    {
        $this->registerEvent = $registerEvent;
        $this->processICEEvent = $processICEEvent;
        $this->processACEEvent = $processACEEvent;
        $this->processDICEEvent = $processDICEEvent;
    }

    /**
     * @param array $payload
     *
     * @return string|null
     */
    public function process($payload)
    {
        $this->registerEvent->register(
            $payload['callid'],
            'ice',
            $payload
        );

        switch ($payload['event']) {
            case 'ice':
                $response = $this->processICEEvent->process(
                    $payload['cli']
                );

                break;
            case 'ace':
                $response = $this->processACEEvent->process();

                break;
            case 'dice':
                $response = $this->processDICEEvent->process();

                break;
            default:
                $response = [];
        }

        $this->registerEvent->register(
            $payload['callid'],
            'response',
            $response
        );

        return $response;
    }
}