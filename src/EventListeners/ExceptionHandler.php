<?php

namespace App\EventListeners;

use App\Helper\EntityFactoryException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionHandler implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['handleEntityException', 0],
                ['handle404Exception', 1],
                ['handleGenericException', -1]
            ]
        ];
    }

    public function handle404Exception(ExceptionEvent $event)
    {
        if ($event->getThrowable() instanceof NotFoundHttpException) {
            $event->setResponse(new JsonResponse([
                'mensagem' => 'Recurso não encontrado'
            ], 404));
        }
    }

    public function handleEntityException(ExceptionEvent $event)
    {
        if ($event->getThrowable() instanceof EntityFactoryException) {
            $event->setResponse(new JsonResponse('', 400));
        }
    }

    public function handleGenericException(ExceptionEvent $event)
    {
        $this->logger->critical('Uma excessão ocorreu: {stack}', [
            'stack' => $event->getThrowable()->getTraceAsString()
        ]);
        
        $event->setResponse(new JsonResponse($event->getThrowable(), 500));
    }
}
