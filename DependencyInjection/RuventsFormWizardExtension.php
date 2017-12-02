<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\DependencyInjection;

use Ruvents\FormWizardBundle\Type\StepTypeInterface;
use Ruvents\FormWizardBundle\Type\WizardTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class RuventsFormWizardExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        (new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config')))
            ->load('services.php');

        $container->registerForAutoconfiguration(WizardTypeInterface::class)
            ->addTag('ruvents_form_wizard.wizard_type');

        $container->registerForAutoconfiguration(StepTypeInterface::class)
            ->addTag('ruvents_form_wizard.step_type');
    }
}
