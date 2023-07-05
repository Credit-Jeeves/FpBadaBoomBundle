<?php

namespace Fp\BadaBoomBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerNormalizersPass;

class AddSerializerNormalizersPassTest extends TestCase
{
    /**
     * @test
     */
    public function shouldFindFpBadaBoomNormalizerTags()
    {
        $containerBuilderMock = $this->createContainerBuilderMock();
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(
                $this->equalTo('fp_badaboom.normalizer')
            )
            ->willReturn(array());
        $containerBuilderMock->expects($this->any())
            ->method('getDefinition')
            ->willReturn($this->createDefinitionMock());

        $pass = new AddSerializerNormalizersPass();
        $pass->process($containerBuilderMock);
    }

    /**
     * @test
     */
    public function shouldReplaceFirstArgumentOfSerializerServiceWithTaggedNormalizers()
    {
        $tags = array(
            'a_normalizer_id' => array(),
            'an_other_normalizer_id' => array(),
        );

        $expectedNormalizers = array(
            new Reference('a_normalizer_id'),
            new Reference('an_other_normalizer_id'),
        );

        $serializerDefinitionMock = $this->createDefinitionMock();
        $serializerDefinitionMock->expects($this->once())
            ->method('replaceArgument')
            ->with(
                $this->equalTo($firstArgument = 0),
                $this->equalTo($expectedNormalizers)
            );

        $containerBuilderMock = $this->createContainerBuilderMock();
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn($tags);
        $containerBuilderMock->expects($this->once())
            ->method('getDefinition')
            ->willReturn($serializerDefinitionMock);

        $pass = new AddSerializerNormalizersPass();
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
