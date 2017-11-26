<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StepTypeFacade
{
    private $type;

    private $validator;

    private $formFactory;

    public function __construct(StepTypeInterface $type, ValidatorInterface $validator, FormFactoryInterface $formFactory)
    {
        $this->type = $type;
        $this->validator = $validator;
        $this->formFactory = $formFactory;
    }

    public function resolveOptions(array $options, OptionsResolver $resolver = null): array
    {
        $resolver = $resolver ?? new OptionsResolver();
        $this->type->configureOptions($resolver);

        return $resolver->resolve($options);
    }

    public function canSkip($data, array $options, array & $context): bool
    {
        return $this->type->canSkip($data, $options, $context);
    }

    public function isValid($data, array $options): bool
    {
        return $this->type->isValid($this->validator, $data, $options);
    }

    public function createFormBuilder($data, array $options): FormBuilderInterface
    {
        return $this->type->createFormBuilder($this->formFactory, $data, $options);
    }
}
