<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\DependencyInjection\Compiler\InjectStepTypesPass;
use Ruvents\FormWizardBundle\DependencyInjection\Compiler\InjectWizardTypesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RuventsFormWizardBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new InjectWizardTypesPass())
            ->addCompilerPass(new InjectStepTypesPass());
    }
}
