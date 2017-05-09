<?php

namespace Cubalider\Voip\Tests\Nexmo;

use Cubalider\Voip\Nexmo\Call\LogEvent;
use Cubalider\Voip\ReceiveEvent;
use Cubalider\Voip\Nexmo\ProcessEvent;
use PHPUnit\Framework\TestCase;

class ProcessEventTest extends TestCase
{
    /**
     * @var LogEvent|\PHPUnit_Framework_MockObject_MockObject
     */
    private $logEvent;
    
    /**
     * @var ReceiveEvent[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    private $receiveEventServices;

    /**
     * @var ProcessEvent
     */
    private $processEvent;

    protected function setUp()
    {
        $this->logEvent = $this
            ->getMockBuilder(LogEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->receiveEventServices = [
            $this
                ->getMockBuilder(ReceiveEvent::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        $this->processEvent = new ProcessEvent(
            $this->logEvent,
            $this->receiveEventServices
        );
    }

    public function testProcess()
    {
        $payload = [
            'conversation_uuid' => 'a-conversation-uuid'
        ];

        $this->logEvent
            ->expects($this->once())
            ->method('log')
            ->with(
                $this->equalTo($payload['conversation_uuid']),
                $this->equalTo($payload)
            );
        
        foreach ($this->receiveEventServices as $receiveEventService) {
            $receiveEventService
                ->expects($this->once())
                ->method('receive')
                ->with(
                    $this->equalTo($payload)
                );
        }

        $this->processEvent->process($payload);
    }
}