<?php

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\DependencyInjection\Compiler\WizardTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RuventsFormWizardBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new WizardTypePass());
    }
}
