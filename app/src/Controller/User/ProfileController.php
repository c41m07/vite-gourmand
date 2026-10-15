<?php

namespace App\Controller\User;

use App\Entity\CustomerOrder;
use App\Entity\User;
use App\Exception\User\InvalidCurrentPasswordException;
use App\Form\User\ProfileEditFormType;
use App\Handler\User\ProfileUpdateHandler;
use App\Service\User\ProfileAccountViewBuilder;
use App\Service\User\UserOrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile', methods: ['GET'])]
    public function index(Request $request, ProfileAccountViewBuilder $profileAccountViewBuilder): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $accountView = $profileAccountViewBuilder->build(
            $user,
            $request->query->get('tab'),
            $request->query->get('status')
        );

        return $this->render('user/account.html.twig', [
            'activeTab' => $accountView->activeTab,
            'orders' => $accountView->orders,
            'orderStatusFilter' => $accountView->orderStatusFilter,
            'orderStatusFilters' => $accountView->orderStatusFilters,
            'reviews' => $accountView->reviews,
        ]);
    }

    #[Route('/user/profile/edit', name: 'app_user_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProfileUpdateHandler $updateHandler): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $userEdited = $this->createEditableUser($user);

        $form = $this->createForm(ProfileEditFormType::class, $userEdited);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = (string) $form->get('currentPassword')->getData();
            /** @var string|null $newPassword */
            $newPassword = $form->get('newPassword')->getData();

            try {
                $updateHandler->handle($user, $userEdited, $currentPassword, $newPassword);

                $this->addFlash('success', 'Profil mis à jour.');

                return $this->redirectToRoute('app_user_profile', [
                    'tab' => 'profile',
                ]);
            } catch (ValidationFailedException $e) {
                $this->addValidationErrors($form, $e);
            } catch (InvalidCurrentPasswordException $e) {
                $this->addCurrentPasswordError($form, $e);
            }
        }

        return $this->renderEditForm($form);
    }

    private function createEditableUser(User $user): User
    {
        return (new User())
            ->setFirstName($user->getFirstName())
            ->setLastName($user->getLastName())
            ->setPhone($user->getPhone())
            ->setPostalAddress($user->getPostalAddress())
            ->setEmail($user->getEmail())
            ->setPassword($user->getPassword());
    }

    private function addValidationErrors(FormInterface $form, ValidationFailedException $e): void
    {
        foreach ($e->getViolations() as $violation) {
            $path = (string) $violation->getPropertyPath();

            if ('' !== $path && $form->has($path)) {
                $form->get($path)->addError(new FormError($violation->getMessage()));
                continue;
            }

            $form->addError(new FormError($violation->getMessage()));
        }
    }

    private function addCurrentPasswordError(FormInterface $form, InvalidCurrentPasswordException $e): void
    {
        $form->get('currentPassword')->addError(new FormError($e->getMessage()));
    }

    private function renderEditForm(FormInterface $form): Response
    {
        return $this->render('user/edit_profile.html.twig', [
            'profileForm' => $form,
        ]);
    }

    #[Route('/user/order/{id}', name: 'app_user_order_show', methods: ['GET'])]
    public function showOrder(CustomerOrder $order, UserOrderService $userOrderService): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if (!$userOrderService->isOwnedBy($order, $user)) {
            throw $this->createNotFoundException();
        }

        return $this->render('user/order_show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/user/order/{id}/cancel', name: 'app_user_order_cancel', methods: ['POST'])]
    public function cancelOrder(
        CustomerOrder $order,
        Request $request,
        UserOrderService $userOrderService,
    ): RedirectResponse {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if (!$userOrderService->isOwnedBy($order, $user)) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('cancel_order_' . $order->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Une erreur est survenue lors de la tentative d\'annulation de la commande.');

            return $this->redirectToRoute('app_user_order_show', ['id' => $order->getId()]);
        }

        if (!$userOrderService->canBeModified($order)) {
            $this->addFlash('error', 'Cette commande ne peut plus être annulée.');

            return $this->redirectToRoute('app_user_order_show', ['id' => $order->getId()]);
        }

        $userOrderService->cancel($order, $user);
        $this->addFlash('success', 'Commande annulée avec succès.');

        return $this->redirectToRoute('app_user_profile', [
            'tab' => 'orders',
        ]);
    }

    #[Route('/user/order/{id}/edit', name: 'app_user_order_edit', methods: ['GET'])]
    public function editOrder(CustomerOrder $order, UserOrderService $userOrderService): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if (!$userOrderService->isOwnedBy($order, $user)) {
            throw $this->createNotFoundException();
        }

        if (!$userOrderService->canBeModified($order)) {
            $this->addFlash('error', 'Cette commande ne peut plus être modifiée.');

            return $this->redirectToRoute('app_user_order_show', ['id' => $order->getId()]);
        }

        return $this->render('user/order_edit.html.twig', [
            'order' => $order,
        ]);
    }
}
