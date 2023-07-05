<?php

namespace Fp\BadaBoomBundle\Tests\ChainNode\Sender;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\ChainNode\ChainNodeInterface;
use Fp\BadaBoomBundle\ChainNode\SymfonyExceptionHandlerChainNode;
use BadaBoom\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/10/12
 */
class SymfonyExceptionHandlerChainNodeTest extends TestCase
{
    public function setUp(): void
    {
        ob_start();
    }

    public function tearDown(): void
    {
        ob_end_flush();
        ob_clean();

    }

    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractSender()
    {
        $rc = new \ReflectionClass(SymfonyExceptionHandlerChainNode::class);

        $this->assertTrue($rc->isSubclassOf(AbstractChainNode::class));
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextNode()
    {
        $context = new Context(new \Exception());

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock->expects($this->once())
            ->method('handle')
            ->with($context);

        $sender = new SymfonyExceptionHandlerChainNode($debug = true);

        $sender->nextNode($nextChainNodeMock);

        $sender->handle($context);
    }

    /**
     * @return MockObject|ChainNodeInterface
     */
    protected function createChainNodeMock()
    {
        return $this->createMock(ChainNodeInterface::class);
    }
}
