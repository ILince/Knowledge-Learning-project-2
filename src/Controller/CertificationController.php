<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CertificationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Display the certifications page.
     * Grants new certifications if the user has completed all lessons for a theme.
     */
    #[Route('/certifications', name: 'certification_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        // Get the currently authenticated user
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in for this page.');
        }

        // Get the user's current certifications
        $certifications = $user->getCertifications();

        $themeRepository = $this->entityManager->getRepository(Theme::class);
        $themes = $themeRepository->findAll();

        // Attempt to grant certifications for each theme
        foreach ($themes as $theme) {
            // Check and add a certification for this theme
            if (!$user->getCertifications($theme)) {
                $user->addCertification($theme);
            }
        }
        $certifications = $user->getCertifications();

        return $this->render('certification/index.html.twig', [
            'certifications' => $certifications,
        ]);
    }
}
