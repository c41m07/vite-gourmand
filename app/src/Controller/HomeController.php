<?php

namespace App\Controller;

use App\Repository\DietRepository;
use App\Repository\MenuRepository;
use App\Repository\OpeningHourRepository;
use App\Repository\ReviewRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(OpeningHourRepository $openingHourRepository, ReviewRepository $reviewRepository): Response
    {
//        $openingHours = $openingHourRepository->openingTimerImformation();
//        $dayOrder= ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
//        $groupedOpeningHours = array_fill_keys($dayOrder, []);
//        foreach ($openingHours as $h) {
//            $groupedOpeningHours[$h->getDayOfWeek()][] = $h;
//        }
        $reviews = $reviewRepository->findFiveRandomReviews();
        return $this->render('pages/home.html.twig',[
//            'openingHours' => $groupedOpeningHours,
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


}
