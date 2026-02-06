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
#[Route('/contact', name: 'app_contact')]

final class ContactController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function contact(): Response
    {
        return $this->render('contact/index.html.twig');
    }


}
