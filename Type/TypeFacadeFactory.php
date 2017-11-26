<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Type;

use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TypeFacadeFactory
{
    private $normalizer;

    private $denormalizer;

    private $validator;

    private $formFactory;

    private $wizardTypes;

    private $stepTypes;

    public function __construct(
        NormalizerInterface $normalizer,
        DenormalizerInterface $denormalizer,
        ValidatorInterface $validator,
        FormFactoryInterface $formFactory,
        ContainerInterface $wizardTypes,
        ContainerInterface $stepTypes
    ) {
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
        $this->formFactory = $formFactory;
        $this->wizardTypes = $wizardTypes;
        $this->stepTypes = $stepTypes;
    }

    public function createWizardTypeFacade(string $class): WizardTypeFacade
    {
        return new WizardTypeFacade($this->wizardTypes->get($class), $this->normalizer, $this->denormalizer);
    }

    public function createStepTypeFacade(string $class): StepTypeFacade
    {
        return new StepTypeFacade($this->stepTypes->get($class), $this->validator, $this->formFactory);
    }
}
