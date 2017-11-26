<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\DependencyInjection\Compiler;

use Ruvents\FormWizardBundle\Type\TypeFacadeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InjectStepTypesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('ruvents_form_wizard.step_type', true);
        $types = [];

        foreach ($serviceIds as $id => $tags) {
            $types[$id] = new Reference($id);
        }

        $container->findDefinition(TypeFacadeFactory::class)
            ->setArgument('$stepTypes', ServiceLocatorTagPass::register($container, $types));
    }
}
