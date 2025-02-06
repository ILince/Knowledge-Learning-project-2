<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This controller is used to display the theme widget on the homepage.
 */
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ThemeRepository $themeRepository): Response
    {
        // Fetch themes by name
        $themes = $themeRepository->findBy(['name' => ['Informatique', 'Musique', 'Jardinage']]);

        $themeIds = [];

        // Loop through the fetched themes and map them to their names
        foreach ($themes as $theme) {
            $themeIds[strtolower($theme->getName()) . '_lessons_theme_id'] = $theme->getId();
        }

        $themeIds += [
            'computer_lessons_theme_id' => $themeIds['informatique_lessons_theme_id'] ?? null,
            'music_lessons_theme_id' => $themeIds['musique_lessons_theme_id'] ?? null,
            'gardening_lessons_theme_id' => $themeIds['jardinage_lessons_theme_id'] ?? null,
        ];

        // Pass the theme IDs to the template
        return $this->render('home/index.html.twig', $themeIds);
    }
}
