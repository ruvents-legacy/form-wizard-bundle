<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\Storage\StorageInterface;
use Ruvents\FormWizardBundle\Type\TypeRegistry;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WizardFactory implements WizardFactoryInterface
{
    private $typeRegistry;

    private $storage;

    public function __construct(TypeRegistry $typeRegistry, StorageInterface $storage)
    {
        $this->typeRegistry = $typeRegistry;
        $this->storage = $storage;
    }

    public function create(string $type, $data = null, array $options = []): Wizard
    {
        $type = $this->typeRegistry->getWizardType($type);

        $resolver = new OptionsResolver();
        $this->configureWizardOptions($resolver);
        $options = $type->resolveOptions($options, $resolver);

        if (null === $data) {
            $data = $options['empty_data'];
        }

        if (!is_object($data)) {
            throw new \UnexpectedValueException();
        }

        $storageKey = $options['storage_key'];

        if ($this->storage->has($storageKey)) {
            $normalized = $this->storage->get($storageKey);
            $type->denormalize($normalized, $data, $options);
        }

        $builder = $type->build($options);
        $steps = $this->createSteps($builder->getSteps(), $data);

        return new Wizard($type, $this->storage, $data, $options, $steps);
    }

    private function createSteps(array $config, $data): array
    {
        $steps = [];
        $canSkipContext = [];
        $index = 1;

        foreach ($config as [$name, $type, $options]) {
            $type = $this->typeRegistry->getStepType($type);
            $options = $type->resolveOptions($options);

            if ($type->canSkip($data, $options, $canSkipContext)) {
                continue;
            }

            $steps[$name] = new Step($type, $name, $index++, $data, $options);
        }

        return $steps;
    }

    private function configureWizardOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'storage_key',
            ])
            ->setDefaults([
                'data_class' => null,
                'empty_data' => function (Options $options) {
                    $class = $options['data_class'];

                    if ($class) {
                        return new $class;
                    }

                    return null;
                },
            ])
            ->setAllowedTypes('storage_key', 'string')
            ->setAllowedTypes('data_class', ['null', 'string'])
            ->setAllowedTypes('empty_data', ['null', 'object']);
    }
}
