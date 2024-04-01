<?php

namespace Fp\BadaBoomBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerNormalizersPass;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerEncodersPass;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddChainNodesToManagerPass;
use Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface;
use Fp\BadaBoomBundle\ChainNode\ChainNodeManagerInterface;

class FpBadaBoomBundle extends Bundle
{
    /**
     * @var ExceptionCatcher\ExceptionCatcherInterface
     */
    public static $exceptionCatcher;

    /**
     * @var ChainNode\ChainNodeManagerInterface
     */
    public static $chainNodeManager;
    
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $badaBoomBundleDefinition = new Definition();
        $badaBoomBundleDefinition->setClass(get_class($this));
        $badaBoomBundleDefinition->setSynthetic(true);
        $container->setDefinition('fp_badaboom', $badaBoomBundleDefinition);
        
        if (static::$exceptionCatcher) {
            $exceptionCatcherDefinition = new Definition();
            $exceptionCatcherDefinition->setClass('Fp\BadaBoomBundle\BadaBoomFactory');
            $exceptionCatcherDefinition->setFactory([get_called_class(), 'getExceptionCatcher']);
            $exceptionCatcherDefinition->setPublic(true);
            
            $container->setDefinition('fp_badaboom.exception_catcher', $exceptionCatcherDefinition);
        }

        if (static::$chainNodeManager) {
            $chainNodeManagerDefinition = new Definition();
            $chainNodeManagerDefinition->setClass('Fp\BadaBoomBundle\BadaBoomFactory');
            $chainNodeManagerDefinition->setFactory([get_called_class(), 'getChainNodeManager']);
            $chainNodeManagerDefinition->setPublic(true);

            $container->setDefinition('fp_badaboom.chain_node_manager', $chainNodeManagerDefinition);
        }

        $container->addCompilerPass(new AddSerializerNormalizersPass());
        $container->addCompilerPass(new AddSerializerEncodersPass());
        $container->addCompilerPass(new AddChainNodesToManagerPass());
    }

    /**
     * @return ExceptionCatcher\ExceptionCatcherInterface
     */
    public static function getExceptionCatcher(): ExceptionCatcherInterface
    {
        return static::$exceptionCatcher;
    }

    /**
     * @return ChainNode\ChainNodeManagerInterface
     */
    public static function getChainNodeManager(): ChainNodeManagerInterface
    {
        return static::$chainNodeManager;
    }
    
    public function boot(): void
    {
        /** @var $exceptionCatcher ExceptionCatcherInterface */
        $exceptionCatcher = $this->container->get('fp_badaboom.exception_catcher');
        /** @var $chainNodeManager ChainNodeManagerInterface */
        $chainNodeManager = $this->container->get('fp_badaboom.chain_node_manager');

        $exceptionCatcher->start($this->container->getParameter('kernel.debug'));
        
        foreach ($chainNodeManager->all() as $chainNode) {
            $exceptionCatcher->registerChainNode($chainNode);
        }
    }
}