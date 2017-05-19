<?php

namespace Cubalider\Voip\Tests\Nexmo;

use Cubalider\Voip\Call;
use Cubalider\Voip\PickCall;
use Cubalider\Voip\ListenCompletedEvent;
use Cubalider\Voip\Nexmo\ReceiveCompletedEvent;
use Cubalider\Voip\UnsupportedEventException;
use Cubalider\Voip\CompleteCall;
use PHPUnit\Framework\TestCase;

class ReceiveCompletedEventTest extends TestCase
{
    /**
     * @var PickCall|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pickCall;

    /**
     * @var CompleteCall|\PHPUnit_Framework_MockObject_MockObject
     */
    private $updateCall;

    /**
     * @var ListenCompletedEvent[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    private $listenCompletedEventServices;

    /**
     * @var ReceiveCompletedEvent
     */
    private $receiveCompletedEvent;

    protected function setUp()
    {
        $this->pickCall = $this
            ->getMockBuilder(PickCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->updateCall = $this
            ->getMockBuilder(CompleteCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->listenCompletedEventServices = [
            $this
                ->getMockBuilder(ListenCompletedEvent::class)
                ->getMock()
        ];

        $this->receiveCompletedEvent = new ReceiveCompletedEvent(
            $this->pickCall,
            $this->updateCall,
            $this->listenCompletedEventServices
        );
    }

    public function testReceiveInboundOutbound()
    {
        $id = 'an-internal-id';

        $halfCompletedPayload = [
            'conversation_uuid' => 'a-conversation-uuid',
            'status' => 'completed',
            'direction' => 'inbound',
            'price' => 0.1,
            'start_time' => '2017-05-04T08:33:18.000Z',
            'end_time' => '2017-05-04T08:34:18.000Z',
            'duration' => 60
        ];

        $completedCompletedPayload = [
            'conversation_uuid' => 'a-conversation-uuid',
            'status' => 'completed',
            'direction' => 'outbound',
            'price' => 0.3,
            'start_time' => '2017-05-04T08:33:19.000Z',
            'end_time' => '2017-05-04T08:34:24.000Z',
            'duration' => 65
        ];

        $this->pickCall
            ->expects($this->at(0))
            ->method('pick')
            ->with(
                $this->equalTo('nexmo'),
                $this->equalTo($halfCompletedPayload['conversation_uuid'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $halfCompletedPayload['conversation_uuid'],
                Call::STATUS_NONE
            ));

        $this->updateCall
            ->expects($this->at(0))
            ->method('updateWithInbound')
            ->with(
                $this->equalTo($id),
                $this->equalTo((float) $halfCompletedPayload['price']),
                $this->equalTo(strtotime($halfCompletedPayload['start_time'])),
                $this->equalTo(strtotime($halfCompletedPayload['end_time'])),
                $this->equalTo((int) $halfCompletedPayload['duration'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $halfCompletedPayload['conversation_uuid'],
                (float) $halfCompletedPayload['price'],
                Call::STATUS_HALF,
                (int) $halfCompletedPayload['duration'],
                strtotime($halfCompletedPayload['start_time']),
                strtotime($halfCompletedPayload['end_time'])
            ));

        $this->pickCall
            ->expects($this->at(1))
            ->method('pick')
            ->with(
                $this->equalTo('nexmo'),
                $this->equalTo($completedCompletedPayload['conversation_uuid'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $completedCompletedPayload['conversation_uuid'],
                (float) $halfCompletedPayload['price'],
                Call::STATUS_HALF,
                (int) $halfCompletedPayload['duration'],
                strtotime($halfCompletedPayload['start_time']),
                strtotime($halfCompletedPayload['end_time'])
            ));

        $this->updateCall
            ->expects($this->at(1))
            ->method('updateWithOutbound')
            ->with(
                $this->equalTo($id),
                $this->equalTo($completedCompletedPayload['price'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $completedCompletedPayload['conversation_uuid'],
                (float) $halfCompletedPayload['price'] + (float) $completedCompletedPayload['price'],
                Call::STATUS_COMPLETED,
                (int) $halfCompletedPayload['duration'],
                strtotime($halfCompletedPayload['start_time']),
                strtotime($halfCompletedPayload['end_time'])
            ));

        foreach ($this->listenCompletedEventServices as $listenCompletedEventService) {
            $listenCompletedEventService
                ->expects($this->once())
                ->method('listen')
                ->with(
                    $this->equalTo($id),
                    $this->equalTo(strtotime($halfCompletedPayload['start_time'])),
                    $this->equalTo(strtotime($halfCompletedPayload['end_time'])),
                    $this->equalTo($halfCompletedPayload['duration']),
                    $this->equalTo((float) $halfCompletedPayload['price'] + (float) $completedCompletedPayload['price'])
                );
        }

        $this->receiveCompletedEvent->receive($halfCompletedPayload);
        $this->receiveCompletedEvent->receive($completedCompletedPayload);
    }

    public function testReceiveOutboundInbound()
    {
        $id = 'an-internal-id';

        $halfCompletedPayload = [
            'conversation_uuid' => 'a-conversation-uuid',
            'status' => 'completed',
            'direction' => 'outbound',
            'price' => 0.3,
            'start_time' => '2017-05-04T08:33:19.000Z',
            'end_time' => '2017-05-04T08:34:24.000Z',
            'duration' => 65
        ];

        $completedCompletedPayload = [
            'conversation_uuid' => 'a-conversation-uuid',
            'status' => 'completed',
            'direction' => 'inbound',
            'price' => 0.1,
            'start_time' => '2017-05-04T08:33:18.000Z',
            'end_time' => '2017-05-04T08:34:18.000Z',
            'duration' => 60
        ];

        $this->pickCall
            ->expects($this->at(0))
            ->method('pick')
            ->with(
                $this->equalTo('nexmo'),
                $this->equalTo($halfCompletedPayload['conversation_uuid'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $halfCompletedPayload['conversation_uuid'],
                Call::STATUS_NONE
            ));

        $this->updateCall
            ->expects($this->at(0))
            ->method('updateWithOutbound')
            ->with(
                $this->equalTo($id),
                $this->equalTo($halfCompletedPayload['price'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $halfCompletedPayload['conversation_uuid'],
                (float) $halfCompletedPayload['price'],
                Call::STATUS_NONE
            ));

        $this->pickCall
            ->expects($this->at(1))
            ->method('pick')
            ->with(
                $this->equalTo('nexmo'),
                $this->equalTo($completedCompletedPayload['conversation_uuid'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $halfCompletedPayload['conversation_uuid'],
                (float) $halfCompletedPayload['price'],
                Call::STATUS_HALF
            ));

        $this->updateCall
            ->expects($this->at(1))
            ->method('updateWithInbound')
            ->with(
                $this->equalTo($id),
                $this->equalTo((float) $completedCompletedPayload['price']),
                $this->equalTo(strtotime($completedCompletedPayload['start_time'])),
                $this->equalTo(strtotime($completedCompletedPayload['end_time'])),
                $this->equalTo((int) $completedCompletedPayload['duration'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $completedCompletedPayload['conversation_uuid'],
                (float) $halfCompletedPayload['price'] + (float) $completedCompletedPayload['price'],
                Call::STATUS_COMPLETED,
                (int) $completedCompletedPayload['duration'],
                strtotime($completedCompletedPayload['start_time']),
                strtotime($completedCompletedPayload['end_time'])
            ));

        foreach ($this->listenCompletedEventServices as $listenCompletedEventService) {
            $listenCompletedEventService
                ->expects($this->once())
                ->method('listen')
                ->with(
                    $this->equalTo($id),
                    $this->equalTo(strtotime($completedCompletedPayload['start_time'])),
                    $this->equalTo(strtotime($completedCompletedPayload['end_time'])),
                    $this->equalTo($completedCompletedPayload['duration']),
                    $this->equalTo((float) $halfCompletedPayload['price'] + (float) $completedCompletedPayload['price'])
                );
        }

        $this->receiveCompletedEvent->receive($halfCompletedPayload);
        $this->receiveCompletedEvent->receive($completedCompletedPayload);
    }

    public function testReceiveWithUnsupportedEvent()
    {
        $this->expectException(UnsupportedEventException::class);

        $this->receiveCompletedEvent->receive([
            'status' => 'ringing'
        ]);
    }
}