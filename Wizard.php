<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\Event\WizardEvent;
use Ruvents\FormWizardBundle\Event\WizardEvents;
use Ruvents\FormWizardBundle\Exception\StepNotFoundException;
use Ruvents\FormWizardBundle\Storage\StorageInterface;
use Ruvents\FormWizardBundle\Type\WizardTypeFacade;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Wizard implements \ArrayAccess, \IteratorAggregate, \Countable
{
    private $storage;

    private $dispatcher;

    private $type;

    private $data;

    private $options;

    /**
     * @var Step[] ['step_name' => object Step]
     */
    private $steps;

    /**
     * @var Step[] [1 => object Step]
     */
    private $stepsByIndex;

    /**
     * @param Step[] $steps
     */
    public function __construct(StorageInterface $storage, EventDispatcherInterface $dispatcher, WizardTypeFacade $type, $data, array $options, array $steps)
    {
        $this->storage = $storage;
        $this->dispatcher = $dispatcher;
        $this->type = $type;
        $this->data = $data;
        $this->options = $options;
        $this->steps = $steps;

        $indexes = array_map(function (Step $step) {
            return $step->getIndex();
        }, $steps);
        $this->stepsByIndex = array_combine($indexes, $steps);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function has(string $name): bool
    {
        return isset($this->steps[$name]);
    }

    public function get(string $name): Step
    {
        if (!$this->has($name)) {
            throw new StepNotFoundException($name);
        }

        return $this->steps[$name];
    }

    /**
     * @return Step[]
     */
    public function all(): array
    {
        return $this->steps;
    }

    public function hasByIndex(int $index): bool
    {
        return isset($this->stepsByIndex[$index]);
    }

    public function getByIndex(int $index): Step
    {
        if (!$this->hasByIndex($index)) {
            throw new StepNotFoundException($index);
        }

        return $this->stepsByIndex[$index];
    }

    /**
     * @return Step[]
     */
    public function allIndexed(): array
    {
        return $this->stepsByIndex;
    }

    public function isComplete(): bool
    {
        foreach ($this->steps as $step) {
            if (!$step->isValid()) {
                return false;
            }
        }

        return true;
    }

    public function isValidTill(Step $step): bool
    {
        foreach ($this->steps as $name => $checkStep) {
            if ($step === $checkStep) {
                return true;
            }

            if (!$checkStep->isValid()) {
                return false;
            }
        }

        throw new StepNotFoundException($step);
    }

    public function getLastValid(): ?Step
    {
        $lastValidStep = null;

        foreach ($this->steps as $checkStep) {
            if (!$checkStep->isValid()) {
                break;
            }

            $lastValidStep = $checkStep;
        }

        return $lastValidStep;
    }

    public function getFirstInvalid(): ?Step
    {
        foreach ($this->steps as $checkStep) {
            if (!$checkStep->isValid()) {
                return $checkStep;
            }
        }

        return null;
    }

    public function getPrevious(Step $step): ?Step
    {
        $previousStep = null;

        foreach ($this->steps as $checkStep) {
            if ($checkStep === $step) {
                return $previousStep;
            }

            $previousStep = $checkStep;
        }

        throw new StepNotFoundException($step);
    }

    public function getNext(Step $step): ?Step
    {
        if (!$this->has($step->getName())) {
            throw new StepNotFoundException($step);
        }

        $return = false;

        foreach ($this->steps as $checkStep) {
            if ($return) {
                return $checkStep;
            }

            if ($checkStep === $step) {
                $return = true;
            }
        }

        return null;
    }

    public function save()
    {
        $this->dispatch(WizardEvents::PRE_SAVE);

        $normalized = $this->type->normalize($this->data, $this->options);
        $this->storage->set($this->options['storage_key'], $normalized);

        $this->dispatch(WizardEvents::POST_SAVE);

        return $this;
    }

    public function clear()
    {
        $this->dispatch(WizardEvents::PRE_CLEAR);

        $this->storage->remove($this->options['storage_key']);

        $this->dispatch(WizardEvents::POST_CLEAR);

        return $this;
    }

    public function revalidate()
    {
        foreach ($this->steps as $step) {
            $step->revalidate();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($name): bool
    {
        return $this->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name): Step
    {
        return $this->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($name, $value): void
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($name): void
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     *
     * @return \Traversable|Step[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->steps);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->steps);
    }

    private function dispatch(string $event): void
    {
        $this->dispatcher->dispatch($event, new WizardEvent($this));
    }
}
