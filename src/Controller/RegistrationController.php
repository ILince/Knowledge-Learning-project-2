<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    // Email verification service and logger service
    public function __construct(private EmailVerifier $emailVerifier, private LoggerInterface $logger) {}

    /**
     * Handles user registration. Validates the form, hashes the password, 
     * saves the user to the database, sends the confirmation email, 
     * and redirects the user to the login page.
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Create a new User object and form
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Get the plain password from the form and hash it
                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

                $entityManager->persist($user);
                $entityManager->flush();

                // Log the successful submission
                $this->logger->info('Form successfully submitted, sending confirmation email to ' . $user->getEmail());

                // Send the email confirmation
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('Support@knowledge.com', 'Support'))
                        ->to((string) $user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );

                // Log the email being sent
                $this->logger->info('Confirmation email sent to ' . $user->getEmail());

                // Add a success flash message
                $this->addFlash('success', 'A confirmation email has been sent. Please check your inbox.');

                // Redirect the user to the login page after successful registration
                return $this->redirectToRoute('app_login');
            } else {
                // Log form errors if the form is not valid
                foreach ($form->getErrors(true) as $error) {
                    $this->logger->error('Form error: ' . $error->getMessage());
                }
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Verifies the user's email address when the confirmation link is clicked.
     * If the email is successfully verified, redirects the user to the homepage.
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        // Ensure the user is authenticated before verifying email
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            // Get the current authenticated user
            $user = $this->getUser();

            // Handle email confirmation process
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            // If there's an issue with the email confirmation, show an error message
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            // Redirect to the registration page if there's an error
            return $this->redirectToRoute('app_register');
        }

        // Add a success flash message once the email is verified
        $this->addFlash('success', 'Your email address has been successfully verified.');

        return $this->redirectToRoute('app_home');
    }
}
