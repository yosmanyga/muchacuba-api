<?php

namespace Cubalider\Voip\Tests\Nexmo;

use Cubalider\Voip\StartCall;
use Cubalider\Voip\Nexmo\AnswerCall;
use Cubalider\Voip\Nexmo\Call;
use Cubalider\Voip\Nexmo\Call\ManageStorage;
use MongoDB\Collection;
use PHPUnit\Framework\TestCase;

class AnswerCallTest extends TestCase
{
    /**
     * @var ManageStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manageStorage;

    /**
     * @var StartCall|\PHPUnit_Framework_MockObject_MockObject
     */
    private $addCall;

    /**
     * @var AnswerCall
     */
    private $answerCall;

    protected function setUp()
    {
        $this->manageStorage = $this
            ->getMockBuilder(ManageStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->addCall = $this
            ->getMockBuilder(StartCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->answerCall = new AnswerCall(
            $this->manageStorage,
            $this->addCall
        );
    }

    public function testAnswer()
    {
        $payload = [
            'conversation_uuid' => 'a-conversation-uuid',
            'from' => '+123'
        ];
        $response = 'a-response';

        $collection = $this
            ->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->manageStorage
            ->expects($this->once())
            ->method('connect')
            ->willReturn($collection);

        $collection
            ->expects($this->once())
            ->method('insertOne')
            ->with(new Call(
                $payload['conversation_uuid'],
                $payload
            ));

        $this->addCall
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo('nexmo'),
                $this->equalTo($payload['conversation_uuid']),
                $this->equalTo($payload['from'])
            )
            ->willReturn($response);

        $this->assertEquals(
            $this->answerCall->answer($payload),
            $response
        );
    }
}