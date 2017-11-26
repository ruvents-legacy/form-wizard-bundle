# RUVENTS Form Wizard Bundle

## Controller example

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\WizardType\RegistrationWizardType;
use App\Repository\User\UserRepository;
use Ruvents\FormWizardBundle\WizardFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration/{step}", name="registration", requirements={"step"="\w+"})
     */
    public function __invoke(WizardFactoryInterface $wizardFactory, string $step, Request $request)
    {
        $user = new User();
        $wizard = $wizardFactory->createWizard(RegistrationWizardType::class, $user);
        $step = $wizard->get($step);

        if (!$wizard->isValidTill($step)) {
            return $this->redirectToRoute('registration', [
                'step' => $wizard->getFirstInvalid(),
            ]);
        }

        $form = $step->createForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wizard->save();

            if ($wizard->revalidate()->isComplete()) {
                // persist

                return $this->redirectToRoute('account');
            }

            return $this->redirectToRoute('registration', [
                'step' => $wizard->getNext($step) ?? $wizard->getFirstInvalid(),
            ]);
        }

        return [
            'form' => $form->createView(),
            'wizard' => $wizard,
        ];
    }
}
```
