<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StripePaymentController extends AbstractController
{
    private $courseRepository;
    private $lessonRepository;

    public function __construct(CourseRepository $courseRepository, LessonRepository $lessonRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->lessonRepository = $lessonRepository;
    }

    /**
     * Checkout page where the user can see their cart before proceeding to payment.
     */
    #[Route('/checkout', name: 'checkout')]
    #[IsGranted('ROLE_USER')]
    public function checkoutPage(SessionInterface $session)
    {
        $user = $this->getUser();

        // Check if the user is logged in
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Get the cart items stored in the session
        $cart = $session->get('cart', []);

        // Retrieve the detailed cart items (lessons and courses)
        $items = $this->getCartItems($cart);

        return $this->render('stripe_payment/checkout.html.twig', [
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'items' => $items,
        ]);
    }

    /**
     * Helper method to retrieve the detailed items in the cart (lessons and courses).
     * 
     * @param array $cart - Cart items stored in the session.
     * @return array - A list of cart items with their details.
     */
    private function getCartItems(array $cart): array
    {
        $items = [];

        // Loop through each item in the cart and fetch its details
        foreach ($cart as $key => $quantity) {
            // Extract type (lesson or course) and ID from the cart key
            [$type, $id] = explode('-', $key);

            if ($type === 'lesson') {
                $entity = $this->lessonRepository->find($id);
            } elseif ($type === 'course') {
                $entity = $this->courseRepository->find($id);
            } else {
                continue;  // Skip invalid types
            }

            // If the entity (lesson or course) is found, add it to the cart items array
            if ($entity) {
                $items[] = [
                    'entity' => $entity,
                    'type' => $type,
                    'quantity' => $quantity,
                ];
            }
        }

        return $items;
    }

    /**
     * Creates a Stripe checkout session with the items in the cart.
     */
    #[Route('/create-checkout-session', name: 'create_checkout_session', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createCheckoutSession(SessionInterface $session): JsonResponse
    {
        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        // Get the cart and its items
        $cart = $session->get('cart', []);
        $items = $this->getCartItems($cart);

        $line_items = [];
        // Prepare the line items for Stripe
        foreach ($items as $item) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['entity']->getName(),
                    ],
                    'unit_amount' => $item['entity']->getPrice() * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Create a Stripe session
        $stripeSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        // Return the session ID as JSON response
        return new JsonResponse(['id' => $stripeSession->id]);
    }


    #[Route('/success', name: 'success')]
    #[IsGranted('ROLE_USER')]
    public function success()
    {
        return $this->render('stripe_payment/success.html.twig');
    }

    #[Route('/cancel', name: 'cancel')]
    #[IsGranted('ROLE_USER')]
    public function cancel()
    {
        return $this->render('stripe_payment/cancel.html.twig');
    }
}
