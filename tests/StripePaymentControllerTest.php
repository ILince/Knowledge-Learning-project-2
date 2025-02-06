<?php

namespace App\Tests\Controller;

use App\Entity\Lesson;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StripePaymentControllerTest extends WebTestCase
{
    /**
     * Tests the checkout page when there are items in the cart.
     * 
     * @return void
     */
    public function testCheckoutPageWithItems(): void
    {
        // Create a client for making requests
        $client = static::createClient();

        // Retrieve a test user from the database
        $user = $this->getUserRepository()->findOneBy(['username' => 'johnUser']);

        // Log the user in
        $client->loginUser($user);

        // Retrieve a lesson to add to the cart
        $lesson = $this->getLessonRepository()->findOneBy(['name' => 'Jardiner avec la lune']);

        // Request the checkout page to initiate the process
        $client->request('GET', '/checkout');

        // Retrieve the session and simulate adding the lesson to the cart
        $session = $client->getRequest()->getSession();
        $cart = $session->get('cart', []);
        $cart['lesson-' . $lesson->getId()] = 1; // Add one unit of the selected lesson
        $session->set('cart', $cart);
        $session->save();

        // Request the checkout page again after modifying the cart
        $client->request('GET', '/checkout');

        // Assert that the page loads successfully
        $this->assertResponseIsSuccessful();

        // Assert that the cart items are displayed on the page
        $this->assertSelectorExists('.cart-items');
        $this->assertSelectorTextContains('.cart-item-name', 'Jardiner avec la lune');
        $this->assertSelectorTextContains('.cart-item-total-price', '16 EUR');
    }

    /**
     * Tests creating a checkout session for payment.
     * 
     * @return void
     */
    public function testCreateCheckoutSession(): void
    {
        // Create a client for making requests
        $client = static::createClient();

        // Retrieve a test user from the database
        $user = $this->getUserRepository()->findOneBy(['username' => 'johnUser']);
        $client->loginUser($user);

        // Retrieve a lesson to add to the cart
        $lesson = $this->getLessonRepository()->findOneBy(['name' => 'Jardiner avec la lune']);

        // Request the checkout page to initiate the process
        $client->request('GET', '/checkout');
        $this->assertResponseIsSuccessful();

        // Retrieve the session and simulate adding the lesson to the cart
        $session = $client->getRequest()->getSession();
        $cart = $session->get('cart', []);
        $cart['lesson-' . $lesson->getId()] = 1;
        $session->set('cart', $cart);
        $session->save();

        // Request to create the checkout session via POST
        $client->request('POST', '/create-checkout-session');

        // Assert that the response is successful and contains valid JSON data
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        // Decode the response JSON and assert it contains a valid checkout session ID
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertNotEmpty($responseData['id']);
    }

    /**
     * Tests the success page after a successful payment.
     * 
     * @return void
     */
    public function testSuccessPage(): void
    {
        // Create a client for making requests
        $client = static::createClient();

        // Retrieve a test user from the database
        $user = $this->getUserRepository()->findOneBy(['username' => 'johnUser']);
        $client->loginUser($user);

        // Request the success page after payment
        $crawler = $client->request('GET', '/success');

        // Assert that the page loads successfully and contains the expected text
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Payment Successful');
    }

    /**
     * Tests the cancel page in case of payment cancellation.
     * 
     * @return void
     */
    public function testCancelPage(): void
    {
        // Create a client for making requests
        $client = static::createClient();

        // Retrieve a test user from the database
        $user = $this->getUserRepository()->findOneBy(['username' => 'johnUser']);
        $client->loginUser($user);

        // Request the cancel page after payment failure or cancellation
        $crawler = $client->request('GET', '/cancel');

        // Assert that the page loads successfully and contains the expected text
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Payment Cancelled');
    }

    /**
     * Helper method to retrieve the User repository.
     * 
     * @return \Doctrine\Persistence\ObjectRepository
     */
    private function getUserRepository()
    {
        return static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
    }

    /**
     * Helper method to retrieve the Lesson repository.
     * 
     * @return \Doctrine\Persistence\ObjectRepository
     */
    private function getLessonRepository()
    {
        return static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository(Lesson::class);
    }
}
