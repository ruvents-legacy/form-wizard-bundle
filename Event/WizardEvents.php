<?php
declare(strict_types=1);

namespace Ruvents\FormWizardBundle\Event;

final class WizardEvents
{
    /**
     * @Event("Ruvents\FormWizardBundle\Event\PreInitEvent")
     */
    const PRE_INIT = 'ruvents_form_wizard.pre_init';

    /**
     * @Event("Ruvents\FormWizardBundle\Event\WizardEvent")
     */
    const POST_INIT = 'ruvents_form_wizard.post_init';

    /**
     * @Event("Ruvents\FormWizardBundle\Event\WizardEvent")
     */
    const PRE_SAVE = 'ruvents_form_wizard.pre_save';

    /**
     * @Event("Ruvents\FormWizardBundle\Event\WizardEvent")
     */
    const POST_SAVE = 'ruvents_form_wizard.post_save';

    /**
     * @Event("Ruvents\FormWizardBundle\Event\WizardEvent")
     */
    const PRE_CLEAR = 'ruvents_form_wizard.pre_clear';

    /**
     * @Event("Ruvents\FormWizardBundle\Event\WizardEvent")
     */
    const POST_CLEAR = 'ruvents_form_wizard.post_clear';

    private function __construct()
    {
    }
}
