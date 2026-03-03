<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(ReviewRepository $reviewRepository): Response
    {
        $reviews = $reviewRepository->findRandomsReviews();
        return $this->render('pages/home.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/mentions-legales', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render('pages/legal.html.twig');
    }

    #[Route('/cgv', name: 'app_cgv')]
    public function cgv(): Response
    {
        return $this->render('pages/cgv.html.twig');
    }

    #[Route('/not-found', name: 'app_not_found')]
    public function pageError(): Response
    {
        return $this->render('shared/404.html.twig');
    }

    #[Route('/under-construction', name: 'app_under_construction')]
    public function underConstruction(): Response
    {
        return $this->render('shared/under_construction.html.twig');
    }

}
