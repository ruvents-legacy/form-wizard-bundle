<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\Event\WizardEvent;
use Ruvents\FormWizardBundle\Event\WizardEvents;
use Ruvents\FormWizardBundle\Storage\StorageInterface;
use Ruvents\FormWizardBundle\Type\TypeRegistry;
use Ruvents\FormWizardBundle\Type\WizardBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
        $type->configureOptions($resolver);
        $options = $resolver->resolve($options);

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

        $dispatcher = new EventDispatcher();
        $builder = new WizardBuilder($dispatcher);
        $type->build($builder, $options);
        $steps = $this->createSteps($builder->getSteps(), $data);
        $wizard = new Wizard($type, $this->storage, $dispatcher, $data, $options, $steps);

        $dispatcher->dispatch(WizardEvents::INIT, new WizardEvent($wizard));

        return $wizard;
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

    private function configureWizardOptions(OptionsResolver $resolver): void
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
