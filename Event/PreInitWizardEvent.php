<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PreInitWizardEvent extends Event
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
