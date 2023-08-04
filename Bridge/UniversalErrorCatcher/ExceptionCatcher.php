<?php

namespace Fp\BadaBoomBundle\Bridge\UniversalErrorCatcher;

use Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface;
use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\Context;
use Symfony\Component\ErrorHandler\DebugClassLoader;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
class ExceptionCatcher extends \UniversalErrorCatcher_Catcher implements ExceptionCatcherInterface
{
    private array $chainNodes = array();
    
    public function registerChainNode(ChainNodeInterface $chainNode): void
    {
        if (in_array($chainNode, $this->chainNodes, $strict = true)) {
            return;
        }
        
        $this->chainNodes[] = $chainNode;
        
        $this->registerCallback(function(\Throwable $e) use ($chainNode) {
            $chainNode->handle(new Context($e));
        });
    }
    
    public function start($debug = false): void
    {
        if ($this->isStarted) {
            return;
        }
        
        $this->setThrowSuppressedErrors(false);
        if ($debug) {
            ini_set('display_errors', 1);
            error_reporting(-1);
            DebugClassLoader::enable();
            $this->setThrowRecoverableErrors(true);
        } else {
            ini_set('display_errors', 0);
            $this->setThrowRecoverableErrors(false);
        }

        parent::start();
    }
}
