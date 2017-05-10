<?php

namespace Ruvents\FormWizardBundle;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface WizardTypeInterface
{
    /**
     * @param array $options
     *
     * @return int|string
     */
    public function getStorageKey(array $options);

    /**
     * @param null|int|string $currentStep
     * @param array           $previousSteps
     * @param mixed           $data
     * @param array           $options
     *
     * @return array
     */
    public function getNextSteps($currentStep, array $previousSteps, $data, array $options);

    /**
     * @param mixed      $data
     * @param string|int $step
     * @param array      $options
     *
     * @return bool
     */
    public function isValid($data, $step, array $options);

    /**
     * @param mixed      $data
     * @param string|int $step
     * @param array      $options
     *
     * @return FormBuilderInterface
     */
    public function createFormBuilder($data, $step, array $options);

    /**
     * @param mixed      $data
     * @param string|int $step
     * @param array      $options
     *
     * @return mixed
     */
    public function normalize($data, $step, array $options);

    /**
     * @param mixed      $data
     * @param mixed      $targetData
     * @param string|int $step
     * @param array      $options
     *
     * @return mixed
     */
    public function denormalize($data, $targetData, $step, array $options);

    /**
     * @param OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver);
}
