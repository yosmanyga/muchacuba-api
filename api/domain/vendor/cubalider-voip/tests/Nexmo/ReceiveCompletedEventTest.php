<?php

namespace Cubalider\Voip\Tests\Nexmo;

use Cubalider\Voip\Call;
use Cubalider\Voip\PickCall;
use Cubalider\Voip\ListenCompletedEvent;
use Cubalider\Voip\Nexmo\ReceiveCompletedEvent;
use Cubalider\Voip\UnsupportedEventException;
use Cubalider\Voip\UpdateCall;
use PHPUnit\Framework\TestCase;

class ReceiveCompletedEventTest extends TestCase
{
    /**
     * @var PickCall|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pickCall;

    /**
     * @var UpdateCall|\PHPUnit_Framework_MockObject_MockObject
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
            ->getMockBuilder(UpdateCall::class)
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

        $firstCompletedPayload = [
            'conversation_uuid' => 'a-conversation-uuid',
            'status' => 'completed',
            'direction' => 'inbound',
            'price' => 0.1,
            'start_time' => '2017-05-04T08:33:18.000Z',
            'end_time' => '2017-05-04T08:34:18.000Z',
            'duration' => 60
        ];

        $secondCompletedPayload = [
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
                $this->equalTo($firstCompletedPayload['conversation_uuid'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $firstCompletedPayload['conversation_uuid'],
                Call::STATUS_NONE
            ));

        $this->updateCall
            ->expects($this->at(0))
            ->method('updateWithInbound')
            ->with(
                $this->equalTo($id),
                $this->equalTo((float) $firstCompletedPayload['price']),
                $this->equalTo(strtotime($firstCompletedPayload['start_time'])),
                $this->equalTo(strtotime($firstCompletedPayload['end_time'])),
                $this->equalTo((int) $firstCompletedPayload['duration'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $firstCompletedPayload['conversation_uuid'],
                (float) $firstCompletedPayload['price'],
                Call::STATUS_FIRST,
                (int) $firstCompletedPayload['duration'],
                strtotime($firstCompletedPayload['start_time']),
                strtotime($firstCompletedPayload['end_time'])
            ));

        $this->pickCall
            ->expects($this->at(1))
            ->method('pick')
            ->with(
                $this->equalTo('nexmo'),
                $this->equalTo($secondCompletedPayload['conversation_uuid'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $secondCompletedPayload['conversation_uuid'],
                (float) $firstCompletedPayload['price'],
                Call::STATUS_FIRST,
                (int) $firstCompletedPayload['duration'],
                strtotime($firstCompletedPayload['start_time']),
                strtotime($firstCompletedPayload['end_time'])
            ));

        $this->updateCall
            ->expects($this->at(1))
            ->method('updateWithOutbound')
            ->with(
                $this->equalTo($id),
                $this->equalTo($secondCompletedPayload['price'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $secondCompletedPayload['conversation_uuid'],
                (float) $firstCompletedPayload['price'] + (float) $secondCompletedPayload['price'],
                Call::STATUS_SECOND,
                (int) $firstCompletedPayload['duration'],
                strtotime($firstCompletedPayload['start_time']),
                strtotime($firstCompletedPayload['end_time'])
            ));

        foreach ($this->listenCompletedEventServices as $listenCompletedEventService) {
            $listenCompletedEventService
                ->expects($this->once())
                ->method('listen')
                ->with(
                    $this->equalTo($id),
                    $this->equalTo(strtotime($firstCompletedPayload['start_time'])),
                    $this->equalTo(strtotime($firstCompletedPayload['end_time'])),
                    $this->equalTo($firstCompletedPayload['duration']),
                    $this->equalTo((float) $firstCompletedPayload['price'] + (float) $secondCompletedPayload['price'])
                );
        }

        $this->receiveCompletedEvent->receive($firstCompletedPayload);
        $this->receiveCompletedEvent->receive($secondCompletedPayload);
    }

    public function testReceiveOutboundInbound()
    {
        $id = 'an-internal-id';

        $firstCompletedPayload = [
            'conversation_uuid' => 'a-conversation-uuid',
            'status' => 'completed',
            'direction' => 'outbound',
            'price' => 0.3,
            'start_time' => '2017-05-04T08:33:19.000Z',
            'end_time' => '2017-05-04T08:34:24.000Z',
            'duration' => 65
        ];

        $secondCompletedPayload = [
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
                $this->equalTo($firstCompletedPayload['conversation_uuid'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $firstCompletedPayload['conversation_uuid'],
                Call::STATUS_NONE
            ));

        $this->updateCall
            ->expects($this->at(0))
            ->method('updateWithOutbound')
            ->with(
                $this->equalTo($id),
                $this->equalTo($firstCompletedPayload['price'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $firstCompletedPayload['conversation_uuid'],
                (float) $firstCompletedPayload['price'],
                Call::STATUS_NONE
            ));

        $this->pickCall
            ->expects($this->at(1))
            ->method('pick')
            ->with(
                $this->equalTo('nexmo'),
                $this->equalTo($secondCompletedPayload['conversation_uuid'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $firstCompletedPayload['conversation_uuid'],
                (float) $firstCompletedPayload['price'],
                Call::STATUS_FIRST
            ));

        $this->updateCall
            ->expects($this->at(1))
            ->method('updateWithInbound')
            ->with(
                $this->equalTo($id),
                $this->equalTo((float) $secondCompletedPayload['price']),
                $this->equalTo(strtotime($secondCompletedPayload['start_time'])),
                $this->equalTo(strtotime($secondCompletedPayload['end_time'])),
                $this->equalTo((int) $secondCompletedPayload['duration'])
            )
            ->willReturn(new Call(
                $id,
                'nexmo',
                $secondCompletedPayload['conversation_uuid'],
                (float) $firstCompletedPayload['price'] + (float) $secondCompletedPayload['price'],
                Call::STATUS_SECOND,
                (int) $secondCompletedPayload['duration'],
                strtotime($secondCompletedPayload['start_time']),
                strtotime($secondCompletedPayload['end_time'])
            ));

        foreach ($this->listenCompletedEventServices as $listenCompletedEventService) {
            $listenCompletedEventService
                ->expects($this->once())
                ->method('listen')
                ->with(
                    $this->equalTo($id),
                    $this->equalTo(strtotime($secondCompletedPayload['start_time'])),
                    $this->equalTo(strtotime($secondCompletedPayload['end_time'])),
                    $this->equalTo($secondCompletedPayload['duration']),
                    $this->equalTo((float) $firstCompletedPayload['price'] + (float) $secondCompletedPayload['price'])
                );
        }

        $this->receiveCompletedEvent->receive($firstCompletedPayload);
        $this->receiveCompletedEvent->receive($secondCompletedPayload);
    }

    public function testReceiveWithUnsupportedEvent()
    {
        $this->expectException(UnsupportedEventException::class);

        $this->receiveCompletedEvent->receive([
            'status' => 'ringing'
        ]);
    }
}