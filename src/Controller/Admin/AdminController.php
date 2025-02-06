<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Theme;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // Route for the admin dashboard
    #[Route('/', name: 'admin_backoffice')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Fetch all entities from the database
        $courses = $entityManager->getRepository(Course::class)->findAll();
        $lessons = $entityManager->getRepository(Lesson::class)->findAll();
        $themes = $entityManager->getRepository(Theme::class)->findAll();
        $users = $entityManager->getRepository(User::class)->findAll();

        // Render the backoffice dashboard with the data
        return $this->render('Backoffice/index.html.twig', [
            'courses' => $courses,
            'lessons' => $lessons,
            'themes' => $themes,
            'users' => $users,
        ]);
    }
}
