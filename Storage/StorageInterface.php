<?php

namespace Ruvents\FormWizardBundle\Storage;

use Ruvents\FormWizardBundle\Exception\StepDataNotFoundException;

interface StorageInterface
{
    /**
     * @param string     $key
     * @param int|string $step
     *
     * @return mixed
     *
     * @throws StepDataNotFoundException
     */
    public function getStepData($key, $step);

    /**
     * @param string     $key
     * @param int|string $step
     *
     * @return bool
     */
    public function hasStepData($key, $step);

    /**
     * @param string     $key
     * @param int|string $step
     * @param mixed      $data
     */
    public function setStepData($key, $step, $data);

    /**
     * @param string     $key
     * @param int|string $step
     */
    public function removeStepData($key, $step);

    /**
     * @param string $key
     */
    public function clear($key);
}
