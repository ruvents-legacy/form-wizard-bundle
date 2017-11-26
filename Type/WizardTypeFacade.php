<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class WizardTypeFacade
{
    private $type;

    private $normalizer;

    private $denormalizer;

    public function __construct(WizardTypeInterface $type, NormalizerInterface $normalizer, DenormalizerInterface $denormalizer)
    {
        $this->type = $type;
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
    }

    public function resolveOptions(array $options, OptionsResolver $resolver = null): array
    {
        $resolver = $resolver ?? new OptionsResolver();
        $this->type->configureOptions($resolver);

        return $resolver->resolve($options);
    }

    public function build(array $options): WizardBuilder
    {
        $builder = new WizardBuilder();
        $this->type->build($builder, $options);

        return $builder;
    }

    public function normalize($data, array $options)
    {
        return $this->type->normalize($this->normalizer, $data, $options);
    }

    public function denormalize($normalized, $data, array $options): void
    {
        $this->type->denormalize($this->denormalizer, $normalized, $data, $options);
    }
}
