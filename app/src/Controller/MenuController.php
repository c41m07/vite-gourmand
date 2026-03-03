<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\AllergenRepository;
use App\Repository\DietRepository;
use App\Repository\DishTypeRepository;
use App\Repository\MenuRepository;
use App\Repository\OpeningHourRepository;
use App\Repository\ReviewRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/menu', name: 'app_menu')]
final class MenuController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function menus(MenuRepository $menuRepository, DietRepository $dietRepository, ThemeRepository $themeRepository): Response
    {
        $diets = $dietRepository->findBy([], ['name' => 'ASC']);
        $themes = $themeRepository->findBy([], ['name' => 'ASC']);
        $menus = $menuRepository->findActiveMenus();
        return $this->render('menu/index.html.twig', [
            'themes' => $themes,
            'diets' => $diets,
            'menus' => $menus,
        ]);
    }

    #[Route('/{id}', name: '_show')]
    public function show(Menu $menu, AllergenRepository $allergenRepository, DishTypeRepository $dishTypeRepository): Response
    {
        if (!$menu->isActive()) {
            throw $this->createNotFoundException();
        }

        $dishesByType = $dishTypeRepository->findDishesByMenu($menu);
        $allergens=$allergenRepository->findByMenu($menu);
        return $this->render('menu/show.html.twig', [
            'allergens' => $allergens,
            'menu' => $menu,
            'dishesByType' => $dishesByType,
        ]);
    }



}
