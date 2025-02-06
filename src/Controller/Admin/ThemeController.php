<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use App\Form\ThemeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/theme')]
#[IsGranted('ROLE_ADMIN')]
final class ThemeController extends AbstractController
{
    /**
     * Displays a list of all themes.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route(name: 'app_theme_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $themes = $entityManager->getRepository(Theme::class)->findAll();

        return $this->render('Backoffice/theme/index.html.twig', [
            'themes' => $themes,
        ]);
    }

    /**
     * Handles the creation of a new theme.
     *
     * @param Request $request The HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route('/new', name: 'app_theme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();

            $theme->setCreatedBy($currentUser);
            $theme->setUpdatedBy($currentUser);

            $entityManager->persist($theme);
            $entityManager->flush();

            $this->addFlash('success', 'The theme has been successfully created.');

            return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backoffice/theme/new.html.twig', [
            'theme' => $theme,
            'form' => $form,
        ]);
    }

    /**
     * Displays details of a specific theme.
     *
     * @param Theme $theme The theme entity.
     * @return Response
     */
    #[Route('/{id}', name: 'app_theme_show', methods: ['GET'])]
    public function show(Theme $theme): Response
    {
        return $this->render('Backoffice/theme/show.html.twig', [
            'theme' => $theme,
        ]);
    }

    /**
     * Handles editing of an existing theme.
     *
     * @param Request $request The HTTP request.
     * @param Theme $theme The theme entity.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_theme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Theme $theme, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $theme->setUpdatedAt(new \DateTime());
            $theme->setUpdatedBy($currentUser);

            $entityManager->flush();

            $this->addFlash('success', 'The theme has been successfully updated.');

            return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backoffice/theme/edit.html.twig', [
            'theme' => $theme,
            'form' => $form,
        ]);
    }

    /**
     * Deletes a theme.
     *
     * @param Request $request The HTTP request.
     * @param Theme $theme The theme entity.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route('/{id}', name: 'app_theme_delete', methods: ['POST'])]
    public function delete(Request $request, Theme $theme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $theme->getId(), $request->request->get('_token'))) {
            $entityManager->remove($theme);
            $entityManager->flush();

            $this->addFlash('success', 'The theme has been successfully deleted.');
        }

        return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
    }
}
