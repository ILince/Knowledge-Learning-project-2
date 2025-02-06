<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Form\LessonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/lesson')]
#[IsGranted('ROLE_ADMIN')]
final class LessonController extends AbstractController
{
    /**
     * Displays the list of lessons.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route(name: 'app_lesson_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $lessons = $entityManager->getRepository(Lesson::class)->findAll();

        return $this->render('Backoffice/lesson/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    /**
     * Creates a new lesson.
     *
     * @param Request $request The HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route('/new', name: 'app_lesson_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();
            $lesson->setCreatedBy($currentUser);
            $lesson->setUpdatedBy($currentUser);

            $entityManager->persist($lesson);
            $entityManager->flush();

            $this->addFlash('success', 'The lesson has been successfully created.');

            return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backoffice/lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    /**
     * Displays a specific lesson.
     *
     * @param Lesson $lesson The lesson entity.
     * @return Response
     */
    #[Route('/{id}', name: 'app_lesson_show', methods: ['GET'])]
    public function show(Lesson $lesson): Response
    {
        return $this->render('Backoffice/lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Edits an existing lesson.
     *
     * @param Request $request The HTTP request.
     * @param Lesson $lesson The lesson entity.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_lesson_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lesson->setUpdatedAt(new \DateTime());
            $lesson->setUpdatedBy($currentUser);

            $entityManager->flush();

            $this->addFlash('success', 'The lesson has been successfully updated.');

            return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backoffice/lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    /**
     * Deletes a lesson.
     *
     * @param Request $request The HTTP request.
     * @param Lesson $lesson The lesson entity.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route('/{id}', name: 'app_lesson_delete', methods: ['POST'])]
    public function delete(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $lesson->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();

            $this->addFlash('success', 'The lesson has been successfully deleted.');
        }

        return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
    }
}
