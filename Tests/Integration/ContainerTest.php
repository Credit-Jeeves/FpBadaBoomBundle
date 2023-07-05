<?php

namespace Fp\BadaBoomBundle\Tests\Integration;

use BadaBoom\Context;
use BadaBoom\Serializer\Encoder\TextEncoder;
use BadaBoom\Serializer\Normalizer\ContextNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ContainerTest extends TestCase
{
    public static ContainerInterface $container;

    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/app/AppKernel.php';

        $kernel = new \AppKernel();
        $kernel->boot();

        self::$container = $kernel->getContainer();
    }

    /**
     * @test
     */
    public function shouldGetContextNormalizer()
    {
        $contextNormalizer = self::$container->get('fp_badaboom.normalizer.context');

        $this->assertInstanceOf(ContextNormalizer::class, $contextNormalizer);
    }

    /**
     * @test
     */
    public function shouldGetTextEncoder()
    {
        $textEncoder = self::$container->get('fp_badaboom.encoder.text');

        $this->assertInstanceOf(TextEncoder::class, $textEncoder);
    }

    /**
     * @test
     */
    public function shouldGetSerializer()
    {
        $serializer = self::$container->get('fp_badaboom.serializer');

        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }

    /**
     * @test
     */
    public function shouldGetSerializerWhichSupportsNormalizationOfContext()
    {
        $serializer = self::$container->get('fp_badaboom.serializer');

        $this->assertTrue($serializer->supportsNormalization($this->createContextMock()));
    }

    /**
     * @test
     */
    public function shouldGetSerializerWhichSupportsEncodingToTextFormat()
    {
        $serializer = self::$container->get('fp_badaboom.serializer');

        $this->assertTrue($serializer->supportsEncoding('text'));
    }

    /**
     * @test
     */
    public function shouldGetSerializerWhichSupportsEncodingToLineFormat()
    {
        $serializer = self::$container->get('fp_badaboom.serializer');

        $this->assertTrue($serializer->supportsEncoding('line'));
    }

    protected function createContextMock()
    {
        return $this
            ->getMockBuilder(Context::class)
            ->setConstructorArgs(array(new \Exception))
            ->getMock();
    }
}
