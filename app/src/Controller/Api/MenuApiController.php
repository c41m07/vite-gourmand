<?php

namespace App\Controller\Api;

use App\Dto\MenuApi\MenuApiFiltersDto;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class MenuApiController extends AbstractController
{
    #[Route('/api/menus', name: 'api_menus', methods: ['GET'])]
    public function list(
        #[MapQueryString(
            validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true]
        )]
        MenuApiFiltersDto $filters,
        MenuRepository $menuRepository,
    ): JsonResponse {
        $menus = $menuRepository->searchPublic($filters);

        return $this->json([
            'body' => $this->renderView('menu/components/_list.html.twig', ['menus' => $menus]),
            'count' => count($menus),
        ]);
    }
}
