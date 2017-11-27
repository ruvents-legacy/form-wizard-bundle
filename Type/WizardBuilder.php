<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Type;

use Ruvents\FormWizardBundle\Event\PreInitWizardEvent;
use Ruvents\FormWizardBundle\Event\WizardEvent;
use Ruvents\FormWizardBundle\Event\WizardEvents;
use Ruvents\FormWizardBundle\Step;
use Ruvents\FormWizardBundle\Storage\StorageInterface;
use Ruvents\FormWizardBundle\Wizard;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WizardBuilder
{
    private $storage;

    private $typeFacadeFactory;

    private $dispatcher;

    private $type;

    private $options;

    private $data;

    private $stepsConfig = [];

    public function __construct(StorageInterface $storage, TypeFacadeFactory $typeFacadeFactory, WizardTypeFacade $type, array $options)
    {
        $this->storage = $storage;
        $this->typeFacadeFactory = $typeFacadeFactory;
        $this->dispatcher = new EventDispatcher();
        $this->type = $type;
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function addStep(string $name, string $type, array $options = [])
    {
        $this->stepsConfig[] = [$name, $type, $options];

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

    public function getWizard(): Wizard
    {
        if (null === $data = $this->data) {
            $data = $this->options['empty_data'];
        }

        if (!is_object($data)) {
            throw new \UnexpectedValueException();
        }

        $storageKey = $this->options['storage_key'];

        if ($this->storage->has($storageKey)) {
            $normalized = $this->storage->get($storageKey);
            $this->type->denormalize($normalized, $data, $this->options);
        }

        $this->dispatcher->dispatch(WizardEvents::PRE_INIT, new PreInitWizardEvent($data));

        $steps = $this->buildSteps($data);
        $wizard = new Wizard($this->storage, $this->dispatcher, $this->type, $data, $this->options, $steps);

        $this->dispatcher->dispatch(WizardEvents::POST_INIT, new WizardEvent($wizard));

        return $wizard;
    }

    private function buildSteps($data): array
    {
        $steps = [];
        $canSkipContext = [];
        $index = 1;

        foreach ($this->stepsConfig as [$name, $type, $options]) {
            $type = $this->typeFacadeFactory->createStepTypeFacade($type);
            $options = $type->resolveOptions($options);

            if ($type->canSkip($data, $options, $canSkipContext)) {
                continue;
            }

            $steps[$name] = new Step($type, $name, $index++, $data, $options);
        }

        return $steps;
    }
}
