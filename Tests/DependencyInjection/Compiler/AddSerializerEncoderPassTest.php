<?php

namespace Fp\BadaBoomBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerEncodersPass;

class AddSerializerEncoderPassTest extends TestCase
{
    /**
     * @test
     */
    public function shouldFindFpBadaBoomEncoderTags()
    {
        $containerBuilderMock = $this->createContainerBuilderMock();
        $containerBuilderMock
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(
                $this->equalTo('fp_badaboom.encoder')
            )
            ->willReturn(array());

        $containerBuilderMock
            ->expects($this->any())
            ->method('getDefinition')
            ->willReturn($this->createDefinitionMock());

        $pass = new AddSerializerEncodersPass();
        $pass->process($containerBuilderMock);
    }

    /**
     * @test
     */
    public function shouldReplaceSecondArgumentOfSerializerServiceWithTaggedEncoders()
    {
        $tags = array(
            'an_encoder_id' => array(),
            'another_encoder_id' => array(),
        );

        $expectedEncoders = array(
            new Reference('an_encoder_id'),
            new Reference('another_encoder_id'),
        );

        $serializerDefinitionMock = $this->createDefinitionMock();
        $serializerDefinitionMock
            ->expects($this->once())
            ->method('replaceArgument')
            ->with(
                $this->equalTo($secondArgument = 1),
                $this->equalTo($expectedEncoders)
            );

        $containerBuilderMock = $this->createContainerBuilderMock();
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn($tags);
        $containerBuilderMock->expects($this->once())
            ->method('getDefinition')
            ->willReturn($serializerDefinitionMock);

        $pass = new AddSerializerEncodersPass();
        $pass->process($containerBuilderMock);
    }

    protected function createContainerBuilderMock()
    {
        return $this->createPartialMock(
            ContainerBuilder::class,
            array('findTaggedServiceIds', 'getDefinition')
        );
    }

    protected function createDefinitionMock()
    {
        return $this->createPartialMock(
            Definition::class,
            array('replaceArgument')
        );
    }
}
