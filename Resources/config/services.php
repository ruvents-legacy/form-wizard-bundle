<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ruvents\FormWizardBundle\EventListener\StepNotFoundExceptionListener;
use Ruvents\FormWizardBundle\Storage\SessionStorage;
use Ruvents\FormWizardBundle\Storage\StorageInterface;
use Ruvents\FormWizardBundle\Type\TypeFacadeFactory;
use Ruvents\FormWizardBundle\WizardFactory;
use Ruvents\FormWizardBundle\WizardFactoryInterface;

return function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->private();

    $services->set(TypeFacadeFactory::class)
        ->args([
            '$normalizer' => ref('serializer'),
            '$denormalizer' => ref('serializer'),
            '$validator' => ref('validator'),
            '$formFactory' => '@form.factory',
        ]);

    $services->set(SessionStorage::class)
        ->args([
            '$session' => ref('session'),
        ]);

    $services->alias(StorageInterface::class, SessionStorage::class);

    $services->set(WizardFactory::class)
        ->args([
            '$typeFacadeFactory' => ref(TypeFacadeFactory::class),
            '$storage' => ref(StorageInterface::class),
        ]);

    $services->alias(WizardFactoryInterface::class, WizardFactory::class);

    $services->set(StepNotFoundExceptionListener::class)
        ->args([
            '$session' => ref('session'),
        ])
        ->tag('kernel.event_subscriber');
};
