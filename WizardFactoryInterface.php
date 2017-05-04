<?php

namespace Ruvents\FormWizardBundle;

interface WizardFactoryInterface
{
    /**
     * @param string $type
     * @param mixed  $data
     * @param array  $options
     *
     * @return WizardInterface
     */
    public function createWizard($type, $data = null, array $options = []);
}
