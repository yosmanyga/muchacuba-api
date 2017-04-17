<?php

namespace Cubalider\Call\Provider;

class DelegateProcessEvent implements ProcessEvent
{
    /**
     * @var ProcessEvent[]
     */
    private $processEventServices;

    /**
     * @param ProcessEvent[] $processEventServices
     */
    public function __construct(array $processEventServices)
    {
        $this->processEventServices = $processEventServices;
    }

    /**
     * {@inheritdoc}
     */
    public function process($payload)
    {
        foreach ($this->processEventServices as $processEventService) {
            try {
                return $processEventService->process($payload);
            } catch (UnsupportedEventException $e) {
                continue;
            }
        }

        throw new UnsupportedEventException();
    }
}