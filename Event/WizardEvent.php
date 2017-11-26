<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Event;

use Ruvents\FormWizardBundle\Wizard;
use Symfony\Component\EventDispatcher\Event;

class WizardEvent extends Event
{
    private $wizard;

    public function __construct(Wizard $wizard)
    {
        $this->wizard = $wizard;
    }

    public function getWizard(): Wizard
    {
        return $this->wizard;
    }
}
