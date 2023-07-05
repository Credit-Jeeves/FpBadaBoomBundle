<?php

namespace Fp\BadaBoomBundle\Tests\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\ChainNode\ChainNodeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Fp\BadaBoomBundle\ChainNode\Provider\RequestProvider;
use BadaBoom\Context;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestProviderTest extends TestCase
{
    /**
     *
     * @test
     */
    public function shouldBeSubClassOfAbstractSender()
    {
        $rc = new \ReflectionClass(RequestProvider::class);

        $this->assertTrue($rc->isSubclassOf(AbstractChainNode::class));
    }

    /**
     *
     * @test
     */
    public function shouldImplementEventSubscriberInterface()
    {
        $rc = new \ReflectionClass(RequestProvider::class);

        $this->assertTrue($rc->implementsInterface(EventSubscriberInterface::class));
    }

    /**
     * @test
     */
    public function shouldListenForRequestEventAndGetRequestFromIt()
    {
        $expectedRequest = Request::createFromGlobals();

        $requestEvent = $this->createRequestEvent();
        $requestEvent->expects($this->once())
            ->method('getRequest')
            ->willReturn($expectedRequest);

        $provider = new RequestProvider();
        $provider->onEarlyKernelRequest($requestEvent);

        $this->assertTrue(isset($expectedRequest->request));
    }

    public function shouldDoNothingIfRequestNotSet()
    {
        $contextMock = $this->createContextMock();
        $contextMock->expects($this->never())
            ->method('setVar');

        $provider = new RequestProvider;

        $provider->handle($contextMock);
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextNode()
    {
        $context = new Context(new \Exception);

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock->expects($this->once())
            ->method('handle')
            ->with($context);

        $provider = new RequestProvider;

        $provider->nextNode($nextChainNodeMock);

        $provider->handle($context);
    }

    /**
     * @test
     */
    public function shouldFillServerSection()
    {
        $expectedServerData = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $request = new Request(
            $query = array(),
            $request = array(),
            $attributes = array(),
            $cookies = array(),
            $files = array(),
            $server = $expectedServerData
        );

        $context = new Context(new \Exception);

        $provider = new RequestProvider;
        $provider->setRequest($request);

        $provider->handle($context);

        $this->assertTrue($context->hasVar('server'));
        $this->assertSame($expectedServerData, $context->getVar('server'));
    }

    /**
     * @test
     */
    public function shouldFillCookieSection()
    {
        $expectedCookieData = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $request = new Request(
            $query = array(),
            $request = array(),
            $attributes = array(),
            $cookies = $expectedCookieData,
            $files = array(),
            $server = array()
        );

        $context = new Context(new \Exception);

        $provider = new RequestProvider;
        $provider->setRequest($request);

        $provider->handle($context);

        $this->assertTrue($context->hasVar('cookies'));
        $this->assertSame($expectedCookieData, $context->getVar('cookies'));
    }

    /**
     * @test
     */
    public function shouldFillGetSection()
    {
        $expectedQueryData = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $request = new Request(
            $query = $expectedQueryData,
            $request = array(),
            $attributes = array(),
            $cookies = array(),
            $files = array(),
            $server = array()
        );

        $context = new Context(new \Exception);

        $provider = new RequestProvider;
        $provider->setRequest($request);

        $provider->handle($context);

        $this->assertTrue($context->hasVar('query'));
        $this->assertSame($expectedQueryData, $context->getVar('query'));
    }

    /**
     * @test
     */
    public function shouldFillRequestSection()
    {
        $expectedRequestData = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $request = new Request(
            $query = array(),
            $request = $expectedRequestData,
            $attributes = array(),
            $cookies = array(),
            $files = array(),
            $server = array()
        );

        $context = new Context(new \Exception);

        $provider = new RequestProvider;
        $provider->setRequest($request);

        $provider->handle($context);

        $this->assertTrue($context->hasVar('request'));
        $this->assertSame($expectedRequestData, $context->getVar('request'));
    }

    /**
     * @return MockObject|Context
     */
    protected function createContextMock()
    {
        return $this->getMockBuilder(Context::class)
            ->setConstructorArgs(array(new \Exception))
            ->getMock();
    }

    protected function createRequestEvent()
    {
        return $this->createMock(RequestEvent::class);
    }

    protected function createChainNodeMock()
    {
        return $this->createMock(ChainNodeInterface::class);
    }
}