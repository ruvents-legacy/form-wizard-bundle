<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\DependencyInjection\Compiler;

use Ruvents\FormWizardBundle\Type\TypeRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InjectWizardTypesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('ruvents_form_wizard.wizard_type', true);
        $types = [];

        foreach ($serviceIds as $id => $tags) {
            $types[$id] = new Reference($id);
        }

        $container->findDefinition(TypeRegistry::class)
            ->setArgument('$wizardTypes', ServiceLocatorTagPass::register($container, $types));
    }
}
