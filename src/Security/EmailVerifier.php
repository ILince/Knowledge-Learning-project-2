<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Psr\Log\LoggerInterface;

class EmailVerifier
{

    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
        $this->logger = $logger; // Logging service for tracking operations
    }

    /**
     * Send an email confirmation to the user with a signed URL for email verification.
     *
     * @param string $verifyEmailRouteName The route name for verifying email
     * @param User $user The user to send the email to
     * @param TemplatedEmail $email The email object that will be sent
     * @throws \InvalidArgumentException If user email is invalid
     * @throws \Exception If an error occurs while sending the email
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        // Ensure the user has a valid email address
        if (!$user->getEmail()) {
            throw new \InvalidArgumentException('User does not have a valid email address.');
        }

        // Log the attempt to send a confirmation email
        $this->logger->info('Attempting to send email confirmation to: ' . $user->getEmail());

        // Generate the signed URL for email verification
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail()
        );

        // Merge the signed URL and expiration data into the email context
        $context = array_merge($email->getContext(), [
            'signedUrl' => $signatureComponents->getSignedUrl(),
            'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
            'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
        ]);

        // Set the merged context into the email object
        $email->context($context);

        try {
            // Attempt to send the email
            $this->mailer->send($email);
            // Log the success of the email sending
            $this->logger->info(sprintf('Confirmation email sent to user ID %d: %s', $user->getId(), $user->getEmail()));
        } catch (\Exception $e) {
            // Log any errors that occur while sending the email
            $this->logger->error('Error sending confirmation email: ' . $e->getMessage());
            throw $e; // Rethrow the exception for further handling
        }
    }

    /**
     * Handle the email confirmation process.
     * Verifies the email confirmation and sets the user's email as verified.
     *
     * @param Request $request The HTTP request containing the confirmation data
     * @param User $user The user whose email is being verified
     * @throws VerifyEmailExceptionInterface If the email verification fails
     * @throws \LogicException If the user is already verified
     */
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        // Prevent access if the user is already verified
        if ($user->isVerified()) {
            throw new \LogicException('User is already verified.');
        }

        try {
            // Validate the email confirmation using the request and user details
            $this->verifyEmailHelper->validateEmailConfirmationFromRequest(
                $request,
                (string) $user->getId(),
                (string) $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $e) {
            // Log and rethrow exceptions if email verification fails
            $this->logger->error('Email verification failed for user ID ' . $user->getId() . ': ' . $e->getReason());
            throw $e;
        }

        // Set the user's email as verified
        $user->setIsVerified(true);

        // Persist the updated user entity to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Log the successful verification of the user
        $this->logger->info('User ID ' . $user->getId() . ' successfully verified.');
    }
}
