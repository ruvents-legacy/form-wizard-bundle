# RUVENTS Form Wizard Bundle

## Example

```php
<?php
declare(strict_types=1);

namespace App\Form\WizardType;

use App\Entity\User\User;
use App\Form\WizardType\EmailStepType;
use App\Form\WizardType\PasswordStepType;
use App\Form\WizardType\TownsStepType;
use App\Form\WizardType\StatusStepType;
use App\Form\WizardType\DataStepType;
use Ruvents\FormWizardBundle\Event\WizardEvent;
use Ruvents\FormWizardBundle\Event\WizardEvents;
use Ruvents\FormWizardBundle\Type\WizardBuilder;
use Ruvents\FormWizardBundle\Type\WizardTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RegistrationWizardType implements WizardTypeInterface
{
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const TOWNS = 'towns';
    const STATUS = 'status';
    const DATA = 'data';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'storage_key' => 'registration',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function build(WizardBuilder $builder, array $options): void
    {
        $builder
            ->addStep(self::EMAIL, EmailStepType::class)
            ->addStep(self::PASSWORD, PasswordStepType::class)
            ->addStep(self::TOWNS, TownsStepType::class)
            ->addStep(self::STATUS, StatusStepType::class)
            ->addStep(self::DATA, DataStepType::class)
            ->addEventListener(WizardEvents::POST_INIT, function (WizardEvent $event) {
                // code
            });
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(NormalizerInterface $normalizer, $data, array $options)
    {
        return $normalizer->normalize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(DenormalizerInterface $denormalizer, $normalized, $data, array $options): void
    {
        $denormalizer->denormalize($normalized, User::class, null, [
            'object_to_populate' => $data,
        ]);
    }
}
```

```php
<?php
declare(strict_types=1);

namespace App\Form\WizardType;

use App\Entity\User\User;
use Ruvents\FormWizardBundle\Type\StepTypeInterface;
use Ruvents\FormWizardBundle\Type\ValidationGroupsTrait;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class EmailStepType implements StepTypeInterface
{
    use ValidationGroupsTrait;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param User $data
     */
    public function canSkip($data, array $options, array & $context): bool
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     *
     * @param User $data
     */
    public function createFormBuilder(FormFactoryInterface $formFactory, $data, array $options): FormBuilderInterface
    {
        return $formFactory
            ->createBuilder(FormType::class, $data, [
                'validation_groups' => $this->getValidationGroups(),
            ])
            ->add('email', EmailType::class, [
                'label' => 'title.email',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidationGroups(): array
    {
        return ['reg_email'];
    }
}

```

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
