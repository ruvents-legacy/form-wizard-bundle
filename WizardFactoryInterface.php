<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\Type\WizardBuilder;

interface WizardFactoryInterface
{
    public function createWizardBuilder(string $type, $data = null, array $options = []): WizardBuilder;

    public function createWizard(string $type, $data = null, array $options = []): Wizard;
}
