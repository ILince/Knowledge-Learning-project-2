<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Handles the login functionality.
     * 
     * Displays the login form and shows any authentication errors.
     *
     * @param AuthenticationUtils $authenticationUtils The service to manage login-related functionality
     * 
     * @return Response The rendered login page
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Retrieve the last authentication error if any
        $error = $authenticationUtils->getLastAuthenticationError();

        // Retrieve the last entered username by the user (pre-filling the form)
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login page with any error and last username
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Handles user logout.
     * 
     * @throws \LogicException
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method will never be executed as it is intercepted by Symfony's firewall
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
