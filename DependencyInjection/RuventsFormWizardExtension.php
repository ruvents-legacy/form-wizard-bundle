<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\DependencyInjection;

use Ruvents\FormWizardBundle\EventListener\StepNotFoundExceptionListener;
use Ruvents\FormWizardBundle\Storage\SessionStorage;
use Ruvents\FormWizardBundle\Storage\StorageInterface;
use Ruvents\FormWizardBundle\Type\StepTypeInterface;
use Ruvents\FormWizardBundle\Type\TypeFacadeFactory;
use Ruvents\FormWizardBundle\Type\WizardTypeInterface;
use Ruvents\FormWizardBundle\WizardFactory;
use Ruvents\FormWizardBundle\WizardFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class RuventsFormWizardExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->autowire('ruvents_form_wizard.factory.default', WizardFactory::class)
            ->setPublic(false);

        $container->setAlias(WizardFactoryInterface::class, 'ruvents_form_wizard.factory.default')
            ->setPublic(false);

        $container->autowire(TypeFacadeFactory::class)
            ->setPublic(false);

        $container->autowire('ruvents_form_wizard.storage.session', SessionStorage::class)
            ->setPublic(false);

        $container->setAlias(StorageInterface::class, 'ruvents_form_wizard.storage.session')
            ->setPublic(false);

        $container->register(StepNotFoundExceptionListener::class)
            ->setPublic(false)
            ->addTag('kernel.event_subscriber');

        $container->registerForAutoconfiguration(WizardTypeInterface::class)
            ->addTag('ruvents_form_wizard.wizard_type');

        $container->registerForAutoconfiguration(StepTypeInterface::class)
            ->addTag('ruvents_form_wizard.step_type');
    }
}
