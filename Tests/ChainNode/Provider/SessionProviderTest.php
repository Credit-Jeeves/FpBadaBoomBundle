<?php

namespace Fp\BadaBoomBundle\Tests\ChainNode\Provider;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\ChainNode\Provider\AbstractProvider;
use BadaBoom\Context;
use Fp\BadaBoomBundle\ChainNode\Provider\SessionProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/10/12
 */
class SessionProviderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass(SessionProvider::class);

        $this->assertTrue($rc->isSubclassOf(AbstractProvider::class));
    }

    /**
     * @test
     */
    public function shouldAddSessionDataToDefaultSection()
    {
        $expectedSessionData = array(
            'foo' => 'foo',
            'bar' => new \stdClass(),
            'olo' => 123
        );

        $sessionMock = $this->createSessionMock();
        $sessionMock->expects($this->once())
            ->method('all')
            ->willReturn($expectedSessionData);

        $contextMock = $this->createContextMock();
        $contextMock->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo('session'),
                $this->equalTo($expectedSessionData)
            );

        $sessionProvider = new SessionProvider($sessionMock);

        $sessionProvider->handle($contextMock);
    }

    /**
     * @test
     */
    public function shouldAddSessionDataToCustomSection()
    {
        $expectedCustomSectionName = 'custom_section_name';

        $contextMock = $this->createContextMock();
        $contextMock->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo($expectedCustomSectionName)
            );

        $sessionProvider = new SessionProvider(
            $this->createSessionMock(),
            $expectedCustomSectionName
        );

        $sessionProvider->handle($contextMock);
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextNode()
    {
        $context = $this->createContextMock();

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock->expects($this->once())
            ->method('handle')
            ->with($context);

        $sessionProvider = new SessionProvider($this->createSessionMock());

        $sessionProvider->nextNode($nextChainNodeMock);

        $sessionProvider->handle($context);
    }

    /**
     * @return MockObject|SessionInterface
     */
    protected function createSessionMock()
    {
        return $this->createMock(SessionInterface::class);
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

    /**
     * @return MockObject|ChainNodeInterface
     */
    protected function createChainNodeMock()
    {
        return $this->createMock(ChainNodeInterface::class);
    }
}