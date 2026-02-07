<?php

namespace App\Controller\Api;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class MenuApiController extends AbstractController
{
    #[Route('/api/menus', name: 'api_menus', methods: ['GET'])]
    public function list(Request $request, MenuRepository $menuRepository): JsonResponse
    {
        $filters = [
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
            'theme' => $request->query->get('theme'),
            'diet' => $request->query->get('diet'),
            'minPersons' => $request->query->get('minPersons'),
            'stock' => $request->query->get('Stock'),
            'isActive' => $request->query->get('isActive'),
        ];

        $menu = $menuRepository->search($filters);


        return $this->json(
            $menu,
            Response::HTTP_OK, [],
            ['groups' =>
                'menu:list'
            ]
        );
    }


}
