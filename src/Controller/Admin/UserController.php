<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    /**
     * Lists all users.
     *
     * @param UserRepository $userRepository The user repository to fetch users.
     * @return Response The rendered response showing all users.
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('Backoffice/user/index.html.twig', [
            'users' => $userRepository->findAll(), // Fetch and pass all users to the view
        ]);
    }

    /**
     * Creates a new user.
     * 
     * @param Request $request The current request to handle the form.
     * @param EntityManagerInterface $entityManager The entity manager to persist the user.
     * @return Response The rendered response with the form or redirect upon success.
     */
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User created successfully.');

            return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backoffice/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Shows a specific user.
     *
     * @param User $user The user entity to show.
     * @return Response The rendered response showing the user's details.
     */
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('Backoffice/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Edits an existing user.
     *
     * @param Request $request The current request to handle the form.
     * @param User $user The user entity to be edited.
     * @param EntityManagerInterface $entityManager The entity manager to flush changes.
     * @return Response The rendered response with the form or redirect upon success.
     */
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the update date and the user who made the change
            $user->setUpdatedAt(new \DateTime());
            $user->setUpdatedBy($currentUser);

            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backoffice/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a user.
     * 
     * @param Request $request The current request to validate CSRF token.
     * @param User $user The user entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager to remove the user.
     * @return Response The redirected response after deletion.
     */
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Check if the CSRF token is valid
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'User deleted successfully.');
        }

        return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
    }
}
