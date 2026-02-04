<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PagesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(ReviewRepository $reviewRepository): Response
    {
        $reviews = $reviewRepository->findFiveRandomReviews();
        return $this->render('pages/home.html.twig',[
            'reviews' => $reviews,
        ]);
    }

    #[Route('/menus', name: 'app_menus')]
    public function menus(MenuRepository $menuRepository): Response
    {
        $menus = $menuRepository->findActiveMenus();
        return $this->render('pages/menus.html.twig',[
            'menus' => $menus,
            ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('pages/contact.html.twig');
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
}
