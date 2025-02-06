<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * Tests the login page is accessible and displays the correct title.
     * 
     * @return void
     */
    public function testLoginPage(): void
    {
        // Create a client for making requests
        $client = static::createClient();

        // Request the login page
        $client->request('GET', '/login');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Assert that the page contains the correct title
        $this->assertSelectorTextContains('h1', 'Log In');
    }

    /**
     * Tests successful login with valid credentials.
     * 
     * @return void
     */
    public function testLoginSuccess(): void
    {
        // Create a client for making requests
        $client = static::createClient();

        // Request the login page
        $crawler = $client->request('GET', '/login');

        // Assert that the response is successful and contains a form
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        // Create a user with valid credentials
        $this->createUser('SecurityTestUser', 'secure_password123!');

        // Fill in the login form with valid credentials
        $form = $crawler->selectButton('Log In')->form();
        $form['username'] = 'SecurityTestUser';
        $form['password'] = 'secure_password123!';

        // Submit the form and check if the user is redirected
        $client->submit($form);
        $this->assertResponseRedirects('/');
    }

    /**
     * Tests login failure with invalid credentials.
     * 
     * @return void
     */
    public function testLoginFailure(): void
    {
        // Create a client for making requests
        $client = static::createClient();

        // Request the login page
        $crawler = $client->request('GET', '/login');

        // Assert that the response is successful and contains a form
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        // Submit invalid credentials
        $form = $crawler->selectButton('Log In')->form();
        $form['username'] = 'invalidUsername';
        $form['password'] = 'invalidPassword';
        $client->submit($form);

        // Follow the redirect after submitting the form
        $crawler = $client->followRedirect();

        // Assert that the failure message is shown
        $this->assertSelectorTextContains('.alert.alert-danger', 'Invalid credentials.');
    }

    /**
     * Helper method to create a user with given credentials.
     * 
     * @param string $email    The user's email
     * @param string $password The user's password
     * 
     * @return void
     */
    private function createUser(string $username, string $password): void
    {
        // Create a new User entity
        $user = (new User())
            ->setUsername($username)
            ->setPassword(password_hash($password, PASSWORD_BCRYPT))
            ->setEmail($username . '@example.com');

        // Persist the user to the database
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }
}
