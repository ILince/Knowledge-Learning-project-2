<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


/**
 * Tests the RegistrationController functionality.
 * Ensures registration page, successful registration, and validation failure are properly handled.
 */
class RegistrationControllerTest extends WebTestCase
{
    /**
     * Tests that the registration page loads correctly.
     *
     * @return void
     */
    public function testRegisterPage(): void
    {
        $client = static::createClient();

        // Request the registration page
        $client->request('GET', '/register');

        // Assert the response is successful and contains the expected header
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Register');
    }

    /**
     * Tests the registration process with valid data and verifies redirection.
     *
     * @return void
     */
    public function testRegisterSuccess(): void
    {
        $client = static::createClient();

        // Request the registration page
        $crawler = $client->request('GET', '/register');

        // Assert the response is successful and contains a form
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        // Fill in the registration form with valid data
        $form = $crawler->selectButton('Create Account')->form([
            'registration_form[username]' => 'RedisterUser',
            'registration_form[email]' => 'RedisterUser@example.com',
            'registration_form[deliveryAddress]' => '123 Main Street',
            'registration_form[plainPassword][first]' => 'ValidPassword123',
            'registration_form[plainPassword][second]' => 'ValidPassword123',
            'registration_form[agreeTerms]' => true,
        ]);

        // Submit the form
        $client->submit($form);

        // Assert that the user is redirected to the login page
        $this->assertResponseRedirects('/login');

        // Follow the redirect and check if a success message is displayed
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success', 'A confirmation email has been sent. Please check your inbox.');
    }


    /**
     * Tests the registration process with invalid data and verifies validation errors.
     *
     * @return void
     */
    public function testRegisterFailure(): void
    {
        $client = static::createClient();

        // Request the registration page
        $crawler = $client->request('GET', '/register');

        // Assert the response is successful and contains a form
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        // Fill in the registration form with invalid data
        $form = $crawler->selectButton('Create Account')->form([
            'registration_form[username]' => '',
            'registration_form[email]' => 'invalid-email',
            'registration_form[deliveryAddress]' => '',
            'registration_form[plainPassword][first]' => '123',
            'registration_form[plainPassword][second]' => '1234',
            'registration_form[agreeTerms]' => false,
        ]);

        // Submit the form
        $client->submit($form);

        // Get the response content to check for validation errors
        $html = $client->getResponse()->getContent();
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

        // Assert the response is still successful, indicating the validation failed and form is still there
        $this->assertResponseIsSuccessful();

        // Assert that validation errors are displayed on the page
        $this->assertSelectorExists('form ul li');
    }
}
