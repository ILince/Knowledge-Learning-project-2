<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Form\CourseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/course')]
#[IsGranted('ROLE_ADMIN')]
final class CourseController extends AbstractController
{
    /**
     * Creates a new course.
     *
     * @param Request $request The HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route('/new', name: 'app_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $course->setCreatedBy($user);
            $course->setUpdatedBy($user);

            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash('success', 'The course has been successfully created.');

            return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backoffice/courses/new.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a specific course.
     *
     * @param Course $course The course entity.
     * @return Response
     */
    #[Route('/{id}', name: 'app_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        return $this->render('Backoffice/courses/show.html.twig', [
            'course' => $course,
        ]);
    }

    /**
     * Edits an existing course.
     *
     * @param Request $request The HTTP request.
     * @param Course $course The course entity.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Get the currently logged-in user
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $course->setUpdatedAt(new \DateTime()); 
            $course->setUpdatedBy($user);

            $entityManager->flush();

            $this->addFlash('success', 'The course has been successfully updated.');

            return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Backoffice/courses/edit.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a course.
     *
     * @param Request $request The HTTP request.
     * @param Course $course The course entity.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @return Response
     */
    #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();

            $this->addFlash('success', 'The course has been successfully deleted.');
        }

        return $this->redirectToRoute('admin_backoffice', [], Response::HTTP_SEE_OTHER);
    }
}
