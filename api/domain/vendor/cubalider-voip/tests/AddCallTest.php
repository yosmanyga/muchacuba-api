<?php

namespace Cubalider\Voip\Tests;

use Cubalider\Voip\Call;
use Cubalider\Voip\Call\ManageStorage;
use Cubalider\Voip\ConnectResponse;
use Cubalider\Voip\ListenIncomingEvent;
use Cubalider\Voip\AddCall;
use Cubalider\Voip\TranslateResponse;
use MongoDB\Collection;
use PHPUnit\Framework\TestCase;

class AddCallTest extends TestCase
{
    /**
     * @var ManageStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manageStorage;

    /**
     * @var ListenIncomingEvent[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    private $listenIncomingEventServices;

    /**
     * @var TranslateResponse[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    private $translateResponseServices;

    /**
     * @var AddCall
     */
    private $addCall;

    protected function setUp()
    {
        $this->manageStorage = $this
            ->getMockBuilder(ManageStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->listenIncomingEventServices = [
            $this
                ->getMockBuilder(ListenIncomingEvent::class)
                ->getMock(),
            $this
                ->getMockBuilder(ListenIncomingEvent::class)
                ->getMock(),
            $this
                ->getMockBuilder(ListenIncomingEvent::class)
                ->getMock(),
        ];

        $this->translateResponseServices = [
            $this
                ->getMockBuilder(TranslateResponse::class)
                ->getMock()
        ];

        $this->addCall = new AddCall(
            $this->manageStorage,
            $this->listenIncomingEventServices,
            $this->translateResponseServices
        );
    }

    public function testAdd()
    {
        $id = 'an-internal-id';
        $provider = 'a-provider';
        $cid = 'a-cid';
        $from = '+123';
        $response = new ConnectResponse('+789');
        $result = 'a-result';

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
                $id,
                $provider,
                $cid
            ));

        $responses = [null, $response, null];
        foreach ($this->listenIncomingEventServices as $i => $listenIncomingEventService) {
            $listenIncomingEventService
                ->expects($this->once())
                ->method('listen')
                ->with(
                    $this->equalTo($from),
                    $this->equalTo($id)
                )
                ->willReturn($responses[$i]);
        }

        foreach ($this->translateResponseServices as $i => $translateResponseService) {
            $translateResponseService
                ->expects($this->once())
                ->method('translate')
                ->with(
                    $this->equalTo($response)
                )
                ->willReturn($result);
        }

        $this->assertEquals(
            $this->addCall->add($provider, $cid, $from, $id),
            $result
        );
    }
}