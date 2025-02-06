<?php

// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\User;
use App\Entity\Certification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    // Injection du service UserPasswordHasherInterface dans le constructeur
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Thème Musique
        $themeMusic = new Theme();
        $themeMusic->setName('Musique');

        $manager->persist($themeMusic);

        // Cursus guitare
        $courseGuitar = new Course();
        $courseGuitar->setName('Cursus d’initiation à la guitare');
        $courseGuitar->setPrice(50.00);
        $courseGuitar->setTheme($themeMusic);
        $manager->persist($courseGuitar);

        // Leçons guitare
        $lessonGuitar1 = new Lesson();
        $lessonGuitar1->setName('Découverte de l’instrument');
        $lessonGuitar1->setPrice(26.00);
        $lessonGuitar1->setCourse($courseGuitar);
        $courseGuitar->addLesson($lessonGuitar1);
        $manager->persist($lessonGuitar1);

        $lessonGuitar2 = new Lesson();
        $lessonGuitar2->setName('Les accords et les gammes');
        $lessonGuitar2->setPrice(26.00);
        $lessonGuitar2->setCourse($courseGuitar);
        $courseGuitar->addLesson($lessonGuitar2);
        $manager->persist($lessonGuitar2);

        // Cursus piano
        $coursePiano = new Course();
        $coursePiano->setName('Cursus d’initiation au piano');
        $coursePiano->setPrice(50.00);
        $coursePiano->setTheme($themeMusic);
        $manager->persist($coursePiano);

        // Leçons piano
        $lessonPiano1 = new Lesson();
        $lessonPiano1->setName('Découverte de l’instrument');
        $lessonPiano1->setPrice(26.00);
        $lessonPiano1->setCourse($coursePiano);
        $coursePiano->addLesson($lessonPiano1);
        $manager->persist($lessonPiano1);

        $lessonPiano2 = new Lesson();
        $lessonPiano2->setName('Les accords et les gammes');
        $lessonPiano2->setPrice(26.00);
        $lessonPiano2->setCourse($coursePiano);
        $coursePiano->addLesson($lessonPiano2);
        $manager->persist($lessonPiano2);

        // 2. Thème Informatique
        $themeIT = new Theme();
        $themeIT->setName('Informatique');
        $manager->persist($themeIT);

        // Cursus développement web
        $courseWebDev = new Course();
        $courseWebDev->setName('Cursus d’initiation au développement web');
        $courseWebDev->setPrice(60.00);
        $courseWebDev->setTheme($themeIT);
        $manager->persist($courseWebDev);

        // Leçons développement web
        $lessonWeb1 = new Lesson();
        $lessonWeb1->setName('Les langages Html et CSS');
        $lessonWeb1->setPrice(32.00);
        $lessonWeb1->setCourse($courseWebDev);
        $courseWebDev->addLesson($lessonWeb1);
        $manager->persist($lessonWeb1);

        $lessonWeb2 = new Lesson();
        $lessonWeb2->setName('Dynamiser votre site avec Javascript');
        $lessonWeb2->setPrice(32.00);
        $lessonWeb2->setCourse($courseWebDev);
        $courseWebDev->addLesson($lessonWeb2);
        $manager->persist($lessonWeb2);

        // 3. Thème Jardinage
        $themeGardening = new Theme();
        $themeGardening->setName('Jardinage');
        $manager->persist($themeGardening);

        // Cursus jardinage
        $courseGardening = new Course();
        $courseGardening->setName('Cursus d’initiation au jardinage');
        $courseGardening->setPrice(30.00);
        $courseGardening->setTheme($themeGardening);
        $manager->persist($courseGardening);

        // Leçons jardinage
        $lessonGardening1 = new Lesson();
        $lessonGardening1->setName('Les outils du jardinier');
        $lessonGardening1->setPrice(16.00);
        $lessonGardening1->setCourse($courseGardening);
        $courseGardening->addLesson($lessonGardening1);
        $manager->persist($lessonGardening1);

        $lessonGardening2 = new Lesson();
        $lessonGardening2->setName('Jardiner avec la lune');
        $lessonGardening2->setPrice(16.00);
        $lessonGardening2->setCourse($courseGardening);
        $courseGardening->addLesson($lessonGardening2);
        $manager->persist($lessonGardening2);

        // 4. Thème Cuisine
        $themeCooking = new Theme();
        $themeCooking->setName('Cuisine');
        $manager->persist($themeCooking);

        // Cursus cuisine
        $courseCooking = new Course();
        $courseCooking->setName('Cursus d’initiation à la cuisine');
        $courseCooking->setPrice(44.00);
        $courseCooking->setTheme($themeCooking);
        $manager->persist($courseCooking);

        // Leçons cuisine
        $lessonCooking1 = new Lesson();
        $lessonCooking1->setName('Les modes de cuisson');
        $lessonCooking1->setPrice(23.00);
        $lessonCooking1->setCourse($courseCooking);
        $courseCooking->addLesson($lessonCooking1);
        $manager->persist($lessonCooking1);

        $lessonCooking2 = new Lesson();
        $lessonCooking2->setName('Les saveurs');
        $lessonCooking2->setPrice(23.00);
        $lessonCooking2->setCourse($courseCooking);
        $courseCooking->addLesson($lessonCooking2);
        $manager->persist($lessonCooking2);

        // Cursus art du dressage culinaire
        $coursePlating = new Course();
        $coursePlating->setName('Cursus d’initiation à l’art du dressage culinaire');
        $coursePlating->setPrice(48.00);
        $coursePlating->setTheme($themeCooking);
        $manager->persist($coursePlating);

        // Leçons art du dressage culinaire
        $lessonPlating1 = new Lesson();
        $lessonPlating1->setName('Mettre en œuvre le style dans l’assiette');
        $lessonPlating1->setPrice(26.00);
        $lessonPlating1->setCourse($coursePlating);
        $coursePlating->addLesson($lessonPlating1);
        $manager->persist($lessonPlating1);

        $lessonPlating2 = new Lesson();
        $lessonPlating2->setName('Harmoniser un repas à quatre plats');
        $lessonPlating2->setPrice(26.00);
        $lessonPlating2->setCourse($coursePlating);
        $coursePlating->addLesson($lessonPlating2);
        $manager->persist($lessonPlating2);


        $manager->flush();

        // Create admin user with all purchased courses
        $admin = new User();
        $admin->setUsername('johnAdmin')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($admin, '1111'))
            ->setEmail('johnAdmin@example.com')
            ->setIsVerified(true)
            ->setDeliveryAddress('123 Admin Street');
        $admin->addCourseWithLessons($courseGuitar);
        $admin->addCourseWithLessons($coursePiano);
        $admin->addCourseWithLessons($courseWebDev);
        $admin->addCourseWithLessons($courseGardening);
        $admin->addCourseWithLessons($courseCooking);
        $admin->addCourseWithLessons($coursePlating);
        $manager->persist($admin);

        // Create standard user with two purchased courses
        $user = new User();
        $user->setUsername('johnUser')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->hasher->hashPassword($user, '1111'))
            ->setEmail('johnUser@example.com')
            ->setIsVerified(true)
            ->setDeliveryAddress('456 User Avenue');


        $user->addCourseWithLessons($courseGuitar);
        $user->addCourseWithLessons($courseWebDev);
        $manager->persist($user);


        $manager->flush();
    }
}
