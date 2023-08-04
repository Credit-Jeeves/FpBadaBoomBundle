<?php
namespace Fp\BadaBoomBundle\ExceptionCatcher;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\Context;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
class ExceptionCatcher implements ExceptionCatcherInterface
{
    protected array $chainNodes = array();
    
    public function registerChainNode(ChainNodeInterface $chainNode): void
    {
        if (in_array($chainNode, $this->chainNodes, $strict = true)) {
            return;
        }
        
        $this->chainNodes[] = $chainNode;
    }

    public function handleException(\Throwable $e): void
    {
        foreach ($this->chainNodes as $chainNode) {
            $chainNode->handle(new Context($e));
        }
    }

    public function start($debug = false): void
    {
        //Basic implementation does not set any handlers. 
        //It uses symfony's exception event to handle exception.
    }
}