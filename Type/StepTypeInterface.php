<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface StepTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void;

    public function canSkip($data, array $options, array & $context): bool;

    public function isValid(ValidatorInterface $validator, $data, array $options): bool;

    public function createFormBuilder(FormFactoryInterface $formFactory, $data, array $options): FormBuilderInterface;
}
