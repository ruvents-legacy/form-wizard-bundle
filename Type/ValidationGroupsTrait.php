<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Type;

use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidationGroupsTrait
{
    /**
     * @see StepTypeInterface::isValid()
     */
    public function isValid(ValidatorInterface $validator, $data, array $options): bool
    {
        $groups = $this->getValidationGroups();

        return 0 === $validator->validate($data, null, $groups)->count();
    }

    abstract protected function getValidationGroups(): array;
}
