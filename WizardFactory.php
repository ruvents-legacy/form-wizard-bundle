<?php

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\Storage\StorageInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WizardFactory implements WizardFactoryInterface, WizardTypeRegistryInterface
{
    /**
     * @var WizardTypeInterface[]
     */
    private $types;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param WizardTypeInterface $type
     *
     * @throws \RuntimeException
     */
    public function registerType(WizardTypeInterface $type)
    {
        $class = get_class($type);

        if (isset($types[$class])) {
            throw new \RuntimeException(
                sprintf('Type %s was already registered or instantiated.', $class)
            );
        }

        $this->types[$class] = $type;
    }

    /**
     * @param string $class
     *
     * @return WizardTypeInterface
     * @throws \InvalidArgumentException
     */
    public function getType($class)
    {
        if (!isset($this->types[$class])) {
            if (!array_key_exists(WizardTypeInterface::class, class_implements($class))) {
                throw new \InvalidArgumentException(
                    sprintf('Wizard type class must implement %s interface.', WizardTypeInterface::class)
                );
            }

            $this->types[$class] = new $class();
        }

        return $this->types[$class];
    }

    /**
     * {@inheritdoc}
     */
    public function createWizard($type, $data = null, array $options = [])
    {
        $type = $this->getType($type);

        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);
        $options = $resolver->resolve($options);

        return new Wizard($this->storage, $type, $data, $options);
    }
}
