<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Type;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WizardBuilder
{
    private $dispatcher;

    private $steps = [];

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function addStep(string $name, string $type, array $options = [])
    {
        $this->steps[] = [$name, $type, $options];

        return $this;
    }

    public function addEventSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);

        return $this;
    }

    public function addEventListener(string $eventName, callable $listener, int $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);

        return $this;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }
}
