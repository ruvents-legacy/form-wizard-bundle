<?php

namespace Ruvents\FormWizardBundle;

interface WizardTypeRegistryInterface
{
    /**
     * @param WizardTypeInterface $type
     */
    public function registerType(WizardTypeInterface $type);

    /**
     * @param string $class
     *
     * @return WizardTypeInterface
     */
    public function getType($class);
}
