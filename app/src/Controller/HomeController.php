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
        $reviews = $reviewRepository->findFiveRandomReviews();
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

    #[Route('/404', name: 'app_404')]
    public function pageerror(): Response
    {
        return $this->render('partials/404.html.twig');
    }
    #[Route('/underconstruct', name: 'app_underconstruct')]
    public function underconstruc(): Response
    {
        return $this->render('partials/underconstruct.html.twig');
    }

}
