<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Type;

class WizardBuilder
{
    private $steps = [];

    public function addStep(string $name, string $type, array $options = [])
    {
        $this->steps[] = [$name, $type, $options];

        return $this;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }
}
