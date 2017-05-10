<?php

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\Exception\CompleteWizardException;
use Ruvents\FormWizardBundle\Exception\InvalidStepException;
use Ruvents\FormWizardBundle\Storage\StorageInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class Wizard implements WizardInterface, WizardConfigInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var WizardTypeInterface
     */
    private $type;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var array
     */
    private $options;

    /**
     * @var int|string
     */
    private $step;

    /**
     * @var array
     */
    private $previousSteps = [];

    /**
     * @var FormInterface
     */
    private $form;

    public function __construct(StorageInterface $storage, WizardTypeInterface $type, $data = null, array $options = [])
    {
        $this->storage = $storage;
        $this->type = $type;
        $this->data = $data;
        $this->options = $options;
        $this->assignNextStep();
        $this->updateData();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousSteps()
    {
        return $this->previousSteps;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextSteps()
    {
        return array_map(function ($step) {
            if (!(is_int($step) || is_string($step) || is_float($step))) {
                throw new \InvalidArgumentException(
                    sprintf('Step must be int, float or string, %s given.', gettype($step))
                );
            }

            return $step;
        }, $this->type->getNextSteps($this->step, $this->previousSteps, $this->data, $this->options));
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return $this->type->isValid($this->data, $this->step, $this->options)
            && (null === $this->form || $this->form->isSubmitted() && $this->form->isValid());
    }

    /**
     * {@inheritdoc}
     */
    public function isComplete()
    {
        return [] === $this->getNextSteps();
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        if (null === $this->form) {
            $this->form = $this->type
                ->createFormBuilder($this->data, $this->step, $this->options)
                ->setData($this->data)
                ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                    $this->data = $event->getForm()->getData();
                }, -1000)
                ->getForm();
        }

        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function proceed()
    {
        if (!$this->isValid()) {
            throw new InvalidStepException($this->step);
        }

        $this->assignNextStep();
        $this->updateData();

        $this->dropForm();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function proceedTo($targetStep)
    {
        while ($targetStep !== $this->step) {
            $this->proceed();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $data = $this->type->normalize($this->data, $this->step, $this->options);
        $this->storage->setStepData($this->getStorageKey(), $this->step, $data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->storage->clear($this->getStorageKey());

        return $this;
    }

    /**
     * @throws CompleteWizardException
     */
    private function assignNextStep()
    {
        $nextSteps = $this->getNextSteps();

        if (empty($nextSteps)) {
            throw new CompleteWizardException();
        }

        if (null !== $this->step) {
            $this->previousSteps[] = $this->step;
        }

        $this->step = reset($nextSteps);
    }

    private function updateData()
    {
        $storageKey = $this->getStorageKey();

        if ($this->storage->hasStepData($storageKey, $this->step)) {
            $data = $this->storage->getStepData($storageKey, $this->step);
            $this->data = $this->type->denormalize($data, $this->data, $this->step, $this->options);
        }
    }

    private function dropForm()
    {
        $this->form = null;
    }

    private function getStorageKey()
    {
        return $this->type->getStorageKey($this->options);
    }
}
