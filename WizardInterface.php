<?php

namespace Ruvents\FormWizardBundle;

use Ruvents\FormWizardBundle\Exception\CompleteWizardException;
use Ruvents\FormWizardBundle\Exception\InvalidStepException;
use Symfony\Component\Form\FormInterface;

interface WizardInterface
{
    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return int|string
     */
    public function getStep();

    /**
     * @return array
     */
    public function getPreviousSteps();

    /**
     * @return array
     */
    public function getNextSteps();

    /**
     * @return FormInterface
     */
    public function getForm();

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return bool
     */
    public function isComplete();

    /**
     * @return $this
     *
     * @throws InvalidStepException|CompleteWizardException
     */
    public function proceed();

    /**
     * @param int|string $targetStep
     *
     * @return $this
     *
     * @throws InvalidStepException|CompleteWizardException
     */
    public function proceedTo($targetStep);

    /**
     * @return $this
     *
     * @throws InvalidStepException
     */
    public function save();

    /**
     * @return $this
     */
    public function clear();
}
