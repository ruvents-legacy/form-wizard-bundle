<?php

namespace Ruvents\FormWizardBundle\Exception;

class InvalidStepException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var int|string
     */
    private $invalidStep;

    /**
     * @param int|string $invalidStep
     */
    public function __construct($invalidStep)
    {
        $this->invalidStep = $invalidStep;
    }

    /**
     * @return int|string
     */
    public function getInvalidStep()
    {
        return $this->invalidStep;
    }
}
