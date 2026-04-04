<?php

namespace App\Controller\Admin;

use App\Service\Admin\AdminDashboardViewBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'app_admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: '_dashboard', methods: ['GET'])]
    public function dashboard(Request $request, AdminDashboardViewBuilder $dashboardViewBuilder): Response
    {
        $viewData = $dashboardViewBuilder->build($request->query->get('tab'));

        return $this->render('admin/dashboard.html.twig', [
            'viewData' => $viewData,
        ]);
    }
}
