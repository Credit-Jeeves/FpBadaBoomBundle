<?php
namespace Fp\BadaBoomBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

use Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface;

class ExceptionListener
{
    public function __construct(protected ExceptionCatcherInterface $exceptionCatcher)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->exceptionCatcher->handleException($event->getThrowable());
    }
}