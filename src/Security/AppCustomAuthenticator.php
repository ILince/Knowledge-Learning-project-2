<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    // Use the TargetPathTrait to handle the last visited page after login
    use TargetPathTrait;

    // Define the route for the login page
    public const LOGIN_ROUTE = 'app_login';

    // Constructor to inject dependencies like the URL generator and user repository
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository
    ) {}

    /**
     * Handles the authentication process by extracting credentials from the login form
     */
    public function authenticate(Request $request): Passport
    {
        // Retrieve the username and password from the form
        $username = $request->request->get('username');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token');

        // Store the last entered username in the session
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        // Create a Passport that includes the user's credentials and badges (CSRF and Remember Me)
        return new Passport(
            // UserBadge retrieves the user based on the username or email
            new UserBadge(
                $username,
                fn(string $identifier) => $this->userRepository->findUserByEmailOrUsername($identifier)
            ),
            // PasswordCredentials checks if the password provided is correct
            new PasswordCredentials($password),
            [
                // CSRF token for authentication protection
                new CsrfTokenBadge('authenticate', $csrfToken),
                // Remember me functionality if enabled
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Handles the response upon successful authentication
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirect to the page the user originally requested, if any
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // If no target path, redirect to the home page
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    /**
     * Retrieves the login URL if authentication is required
     */
    protected function getLoginUrl(Request $request): string
    {
        // Generate the login page URL
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
