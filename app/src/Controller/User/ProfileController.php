<?php

namespace App\Controller\User;

use App\Application\User\Exception\InvalidCurrentPasswordException;
use App\Application\User\Handler\ProfileUpdateHandler;
use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderStatusHistory;
use App\Entity\User;
use App\Form\User\ProfilEditFormType;
use App\Repository\OrderStatusRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
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
    public function index(Request $request): Response
    {
        $activeTab = $request->query->get('tab', 'orders');
        if (!in_array($activeTab, ['orders', 'profile', 'reviews'], true)) {
            $activeTab = 'orders';
        }

        $orders = [];
        $reviews = [];
        $user = $this->getUser();
        if ($user instanceof User) {
            $orders = $user->getCustomerOrders();
            $reviews = $user->getReviews();
        }

        return $this->render('user/account.html.twig', [
            'activeTab' => $activeTab,
            'orders' => $orders,
            'reviews' => $reviews,
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

        $form = $this->createForm(ProfilEditFormType::class, $userEdited);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = (string)$form->get('currentPassword')->getData();
            /** @var string|null $newPassword */
            $newPassword = $form->get('newPassword')->getData();

            try {
                $updateHandler->handle($user, $userEdited, $currentPassword, $newPassword);

                $this->addFlash('success', 'Profil mis a jour');

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

//    TODO Prévoir de créé un Manager pour faire en sorte que les fonction private soit séparer des routes
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
            $path = (string)$violation->getPropertyPath();

            if ($path !== '' && $form->has($path)) {
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
    public function showOrder(CustomerOrder $order): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if ($order->getUser() !== $user) {
            throw $this->createNotFoundException();
        }

        return $this->render('user/order_show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/user/order/{id}/cancel', name: 'app_user_order_cancel', methods: ['POST'])]
    public function cancelOrder(CustomerOrder          $order, Request $request, OrderStatusRepository $orderStatusRepository,
                                EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if ($order->getUser() !== $user) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('cancel_order_' . $order->getid(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Une erreur est survenue lors de la tentative de suppression de la commande.');
        }


        $lastHistory = $order->getCustomerOrderStatusHistories()->last();
        $currentStatusCode = $lastHistory && $lastHistory->getOrderStatus() ? $lastHistory->getOrderStatus()->getCode() : null;

        if ($currentStatusCode !== 'pending') {
            $this->addFlash('error', 'Cette commande ne peux plus être annulée');
            return $this->redirectToRoute('app_user_order_show', ['id' => $order->getId()]);
        }
        $cancelledStatus = $orderStatusRepository->findOneBy(['code' => 'cancelled']);
        if ($cancelledStatus === null) {
            throw new RuntimeException('Status non trouvé');
        }
        $history = new CustomerOrderStatusHistory()
            ->setCustomerOrder($order)
            ->setOrderStatus($cancelledStatus)
            ->setChangedByUser($user)
            ->setChangedAt(new DateTime())
            ->setComment('Commande annulee par le client.');

        $em->persist($history);
        $em->flush();


        $this->addFlash('success', 'Commande annulee avec succes');
        return $this->redirectToRoute('app_user_profile', [
            'tab' => 'orders',
        ]);

    }

    #[Route('/user/order/{id}/edit', name: 'app_user_order_edit', methods: ['GET'])]
    public function deleteOrder(CustomerOrder $order): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if ($order->getUser() !== $user) {
            throw $this->createNotFoundException();
        }
        $lastHistory = $order->getCustomerOrderStatusHistories()->last();
        $currentStatusCode = $lastHistory && $lastHistory->getOrderStatus() ? $lastHistory->getOrderStatus()->getCode() : null;
        if ($currentStatusCode !== 'pending') {
            $this->addFlash('error', 'Cette commande ne peux plus être modifiée');
            return $this->redirectToRoute('app_user_order_show', ['id' => $order->getId()]);
        }

        return $this->render('user/order_edit.html.twig', [
            'order' => $order,
        ]);
    }
}




