<?php
namespace Fp\BadaBoomBundle\ExceptionCatcher;

use BadaBoom\ChainNode\ChainNodeInterface;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
interface ExceptionCatcherInterface
{
    /**
     * @param \BadaBoom\ChainNode\ChainNodeInterface $chainNode
     * 
     * @return void
     */
    function registerChainNode(ChainNodeInterface $chainNode);
    
    /**
     * @param \Throwable $e
     * 
     * @return void
     */
    function handleException(\Throwable $e);

    /**
     * @return void
     */
    function start($debug = false);
}