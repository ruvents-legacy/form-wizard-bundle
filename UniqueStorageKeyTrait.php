<?php

namespace Ruvents\FormWizardBundle;

class UniqueStorageKeyTrait
{
    /**
     * @see WizardTypeInterface::getStorageKey()
     *
     * @param array $options
     *
     * @return string
     */
    public function getStorageKey(
        /** @noinspection PhpUnusedParameterInspection */
        array $options
    ) {
        return uniqid(get_class($this));
    }
}
