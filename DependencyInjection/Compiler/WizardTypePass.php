<?php

namespace Ruvents\FormWizardBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WizardTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('ruvents_form_wizard.wizard_factory');

        $taggedServices = $container->findTaggedServiceIds('ruvents_form_wizard_type');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('registerType', array(new Reference($id)));
        }
    }
}
