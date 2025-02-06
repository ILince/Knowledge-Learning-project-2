<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Lesson;
use App\Entity\LessonProgress;
use App\Repository\CourseRepository;
use App\Repository\LessonProgressRepository;
use App\Repository\LessonRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/course')] 
#[IsGranted('ROLE_USER')] 
class CourseController extends AbstractController
{
    /**
     * Displays the list of courses filtered by theme.
     *
     * @param CourseRepository $courseRepository
     * @param ThemeRepository $themeRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/list', name: 'app_course')]
    public function index(CourseRepository $courseRepository, ThemeRepository $themeRepository, Request $request): Response
    {
        $themes = $themeRepository->findAll();
        $selectedThemeId = $request->query->get('theme');
        $courses = $selectedThemeId ? $courseRepository->findBy(['theme' => $selectedThemeId]) : $courseRepository->findAll();

        return $this->render('course/index.html.twig', [
            'courses' => $courses,
            'themes' => $themes,
        ]);
    }

    /**
     * Displays the details of a course.
     *
     * @param int $id
     * @param CourseRepository $courseRepository
     * @param Security $security
     * @return Response
     */
    #[Route('/course/{id}', name: 'course_details')]
    public function courseDetails(int $id, CourseRepository $courseRepository, Security $security): Response
    {
        $course = $courseRepository->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        $user = $security->getUser();

        return $this->render('course/details.html.twig', [
            'course' => $course,
            'user' => $user,
        ]);
    }

    /**
     * Displays the details of a lesson.
     *
     * @param Lesson $lesson
     * @param LessonProgressRepository $lessonProgressRepository
     * @return Response
     */
    #[Route('/lesson/{id}', name: 'lesson_details')]
    public function lessonDetails(Lesson $lesson, LessonProgressRepository $lessonProgressRepository): Response
    {
        $user = $this->getUser();
        $lessonProgress = $lessonProgressRepository->findOneBy(['user' => $user, 'lesson' => $lesson]);

        return $this->render('lesson/details.html.twig', [
            'lesson' => $lesson,
            'lessonProgress' => $lessonProgress,
            'user' => $user,
        ]);
    }

    /**
     * Validates a lesson and checks if the user qualifies for a certification.
     *
     * @param int $id
     * @param LessonRepository $lessonRepository
     * @param LessonProgressRepository $lessonProgressRepository
     * @param EntityManagerInterface $manager
     * @param Security $security
     * @return Response
     */
    #[Route('/lesson/{id}/validate', name: 'lesson_validate')]
    public function validateLesson(
        int $id,
        LessonRepository $lessonRepository,
        LessonProgressRepository $lessonProgressRepository,
        EntityManagerInterface $manager,
        Security $security
    ): Response {
        $lesson = $lessonRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Lesson not found');
        }

        $user = $security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Check if user already validated the lesson
        $lessonProgress = $lessonProgressRepository->findOneBy([
            'user' => $user,
            'lesson' => $lesson
        ]);

        if (!$lessonProgress) {
            $lessonProgress = new LessonProgress($user, $lesson, true);
            $lessonProgress->setCreatedAt(new \DateTime());
            $lessonProgress->setUpdatedAt(new \DateTime());
            $manager->persist($lessonProgress);
        } else {
            $lessonProgress->setValidated(true);
            $lessonProgress->setUpdatedAt(new \DateTime());
        }

        $manager->flush();

        // Check if all lessons in the theme are validated
        $theme = $lesson->getCourse()->getTheme();
        $allValidated = true;

        foreach ($theme->getCourses() as $course) {
            foreach ($course->getLessons() as $themeLesson) {
                $progress = $lessonProgressRepository->findOneBy([
                    'user' => $user,
                    'lesson' => $themeLesson
                ]);

                if (!$progress || !$progress->isValidated()) {
                    $allValidated = false;
                    break 2;
                }
            }
        }

        if ($allValidated) {
            $certification = new Certification();
            $certification->setUser($user)->setTheme($theme);

            $manager->persist($certification);
            $manager->flush();

            $this->addFlash('success', 'All lessons validated! You have earned your certification.');
        } else {
            $this->addFlash('success', 'Lesson validated! Keep progressing to earn your certification.');
        }

        return $this->redirectToRoute('course_details', ['id' => $lesson->getCourse()->getId()]);
    }

    /**
     * Displays the user's purchased courses.
     *
     * @param Security $security
     * @return Response
     */
    #[Route('/my-courses', name: 'app_my_courses')]
    public function myCourses(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user instanceof \App\Entity\User) {
            throw new \LogicException('The current user is not an instance of App\Entity\User.');
        }

        $purchasedCourses = $user->getPurchasedCourses();

        return $this->render('course/my_courses.html.twig', [
            'courses' => $purchasedCourses,
        ]);
    }

    /**
     * Checks if a course is already in the cart.
     *
     * @param array $cart The cart data.
     * @param int $courseId The course ID to check.
     * @return bool
     */
    private function isCourseInCart(array $cart, int $courseId): bool
    {
        foreach ($cart as $key => $quantity) {
            if (strpos($key, 'course-') === 0) {
                [$type, $id] = explode('-', $key);
                if ((int) $id === $courseId) {
                    return true;
                }
            }
        }
        return false;
    }
}
