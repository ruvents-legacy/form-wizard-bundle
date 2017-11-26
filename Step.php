<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\Type\StepTypeFacade;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

final class Step
{
    private $type;

    private $name;

    private $index;

    private $data;

    private $options;

    private $valid;

    public function __construct(StepTypeFacade $type, string $name, int $index, $data, array $options)
    {
        $this->type = $type;
        $this->name = $name;
        $this->index = $index;
        $this->data = $data;
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isValid(): bool
    {
        if (null === $this->valid) {
            $this->valid = $this->type->isValid($this->data, $this->options);
        }

        return $this->valid;
    }

    public function createFormBuilder(): FormBuilderInterface
    {
        return $this->type->createFormBuilder($this->data, $this->options);
    }

    public function createForm(): FormInterface
    {
        return $this->createFormBuilder()->getForm();
    }

    public function revalidate()
    {
        $this->valid = null;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
