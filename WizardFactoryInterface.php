<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle;

interface WizardFactoryInterface
{
    public function create(string $type, $data = null, array $options = []): Wizard;
}
