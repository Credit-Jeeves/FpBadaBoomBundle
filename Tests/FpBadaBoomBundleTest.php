<?php

namespace Fp\BadaBoomBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Fp\BadaBoomBundle\FpBadaBoomBundle;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerNormalizersPass;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerEncodersPass;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddChainNodesToManagerPass;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FpBadaBoomBundleTest extends TestCase
{
    /**
     * @test
     */
    public function shouldAddSerializerNormalizersPassWhileBuilding()
    {
        $bundle = new FpBadaBoomBundle;
        $container = new ContainerBuilder();
        $bundle->build($container);

        $this->assertTrue(
            $this->hasContainerCompilerPass($container, AddSerializerNormalizersPass::class)
        );
    }

    /**
     * @test
     */
    public function shouldAddSerializerEncodersPassWhileBuilding()
    {
        $bundle = new FpBadaBoomBundle;
        $container = new ContainerBuilder();
        $bundle->build($container);

        $this->assertTrue(
            $this->hasContainerCompilerPass($container, AddSerializerEncodersPass::class)
        );
    }

    /**
     * @test
     */
    public function shouldAddAddChainNodesToManagerPassWhileBuilding()
    {
        $bundle = new FpBadaBoomBundle;
        $container = new ContainerBuilder();
        $bundle->build($container);

        $this->assertTrue(
            $this->hasContainerCompilerPass($container, AddChainNodesToManagerPass::class)
        );
    }

    protected function hasContainerCompilerPass(ContainerInterface $container, string $passClassName): bool
    {
        foreach ($container->getCompilerPassConfig()->getPasses() as $key => $value) {
            if (get_class($value) === $passClassName) {
                return true;
            }
        }

        return false;
    }
}
