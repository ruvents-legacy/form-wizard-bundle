<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface WizardTypeInterface
{
    public function configureOptions(OptionsResolver $resolver);

    public function build(WizardBuilder $builder, array $options);

    public function normalize(NormalizerInterface $normalizer, $data, array $options);

    public function denormalize(DenormalizerInterface $denormalizer, $normalized, $data, array $options): void;
}
