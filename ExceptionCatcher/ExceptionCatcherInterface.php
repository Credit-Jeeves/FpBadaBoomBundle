<?php
namespace Fp\BadaBoomBundle\ExceptionCatcher;

use BadaBoom\ChainNode\ChainNodeInterface;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
interface ExceptionCatcherInterface
{
    function registerChainNode(ChainNodeInterface $chainNode): void;
    
    function handleException(\Throwable $e): void;

    function start($debug = false): void;
}