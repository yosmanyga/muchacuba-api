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
                return $this->processICEEvent->process(
                    $payload['cli'],
                    $payload['to']['endpoint']
                );

                break;
            case 'ace':
                return $this->processACEEvent->process();

                break;
            case 'dice':
                return $this->processDICEEvent->process();

                break;
            default:
                return null;
        }
    }
}