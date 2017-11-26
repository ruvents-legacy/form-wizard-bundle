<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\EventListener;

use Ruvents\FormWizardBundle\Exception\StepNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class StepNotFoundExceptionListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(GetResponseForExceptionEvent $event): void
    {
        if (($exception = $event->getException()) instanceof StepNotFoundException) {
            $event->setException(new NotFoundHttpException($exception->getMessage(), $exception));
        }
    }
}
