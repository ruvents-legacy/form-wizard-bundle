<?php

namespace Ruvents\FormWizardBundle\Storage;

use Ruvents\FormWizardBundle\Exception\StepDataNotFoundException;

abstract class AbstractStorage implements StorageInterface
{
    /**
     * @param string $key
     *
     * @return array
     */
    abstract public function getData($key);

    /**
     * @param string $key
     * @param array  $data
     */
    abstract public function setData($key, array $data);

    /**
     * {@inheritdoc}
     */
    public function getStepData($key, $step)
    {
        if (!$this->hasStepData($key, $step)) {
            throw new StepDataNotFoundException();
        }

        return $this->getData($key)[$step];
    }

    /**
     * {@inheritdoc}
     */
    public function hasStepData($key, $step)
    {
        $data = $this->getData($key);

        return isset($data[$step]);
    }

    /**
     * {@inheritdoc}
     */
    public function setStepData($key, $step, $stepData)
    {
        $data = $this->getData($key);
        $data[$step] = $stepData;
        $this->setData($key, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function removeStepData($key, $step)
    {
        $data = $this->getData($key);
        unset($data[$step]);
        $this->setData($key, $data);
    }
}
