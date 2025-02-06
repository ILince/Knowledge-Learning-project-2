<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\LessonRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller to handle shopping cart functionalities.
 */
#[Route('/cart', name: 'app_cart_')]
#[IsGranted('ROLE_USER')] 
class CartController extends AbstractController
{
    /**
     * Displays the cart contents along with the total price.
     *
     * @param SessionInterface $session
     * @param LessonRepository $lessonRepository
     * @param CourseRepository $courseRepository
     * @return Response
     */
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, LessonRepository $lessonRepository, CourseRepository $courseRepository): Response
    {
        $cart = $session->get('cart', []); 
        $data = [];
        $total = 0;

        foreach ($cart as $key => $quantity) {
            $parts = explode('-', $key);
            if (count($parts) === 2) {
                [$type, $id] = $parts;

                $entity = ($type === 'lesson') ? $lessonRepository->find($id) : $courseRepository->find($id);

                if ($entity) {
                    $data[] = [
                        'entity' => $entity,  
                        'quantity' => $quantity,
                    ];

                    $total += $entity->getPrice() * $quantity;
                }
            }
        }

        return $this->render('cart/index.html.twig', [
            'data' => $data,
            'total' => $total,
        ]);
    }

    /**
     * Adds an item (lesson or course) to the cart.
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param LessonRepository $lessonRepository
     * @param CourseRepository $courseRepository
     * @param UserInterface $user
     * @return Response
     */
    #[Route('/add', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(
        Request $request,
        SessionInterface $session,
        LessonRepository $lessonRepository,
        CourseRepository $courseRepository,
        UserInterface $user
    ): Response {
        // Ensure the user is valid
        if (!$user instanceof User) {
            throw new \Exception('Invalid user');
        }

        // Check if user is verified
        if (!$user->isVerified()) {
            $this->addFlash('error', 'Your account must be verified to make a purchase.');
            return $this->redirectToRoute('app_cart_index'); 
        }

        $id = $request->request->get('id');
        $type = $request->request->get('type');
        $quantity = (int) $request->request->get('quantity', 1);

        // Validate the item type
        if (!in_array($type, ['lesson', 'course'])) {
            $this->addFlash('error', 'Invalid item type.');
            return $this->redirectToRoute('app_cart_index');
        }

        // Retrieve the corresponding entity (lesson or course)
        $entity = ($type === 'lesson') ? $lessonRepository->find($id) : $courseRepository->find($id);

        if (!$entity) {
            $this->addFlash('error', 'Item not found.');
            return $this->redirectToRoute('app_cart_index');
        }

        $cart = $session->get('cart', []);

        $key = $type . '-' . $entity->getId();

        // Check if the item is already in the cart
        if (isset($cart[$key])) {
            $this->addFlash('error', 'This item is already in your cart.');
            return $this->redirectToRoute('app_cart_index');
        }

        // Add item to the cart
        $cart[$key] = $quantity;

        $session->set('cart', $cart);

        $this->addFlash('success', 'Item added to the cart.');
        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * Removes an item from the cart.
     *
     * @param string $type (lesson or course)
     * @param int $id The ID of the item
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/remove/{type}/{id}', name: 'remove')]
    public function remove(string $type, int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); 
        $key = $type . '-' . $id;

        // Remove the item from the cart if it exists
        if (isset($cart[$key])) {
            unset($cart[$key]);
        }

        $session->set('cart', $cart); 
        return $this->redirectToRoute('app_cart_index');
    }
}
