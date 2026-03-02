<?php

namespace App\Controller\Api;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            'stock' => $request->query->get('stock'),
        ];

        $menus = $menuRepository->searchPublic($filters);

        return $this->json([
            'body' => $this->renderView('menu/components/list_card.html.twig', ['menus' => $menus]),
            'count' => count($menus),
        ]);
    }
}
