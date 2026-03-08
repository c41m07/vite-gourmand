<?php

namespace App\Controller\User;

use App\Application\User\Exception\InvalidCurrentPasswordException;
use App\Application\User\Handler\ProfileUpdateHandler;
use App\Entity\User;
use App\Form\User\ProfilEditFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
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
}
