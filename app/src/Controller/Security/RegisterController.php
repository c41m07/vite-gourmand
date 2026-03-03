<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\EmailFactory;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        UserRepository $userRepository,
        EmailFactory $emailFactory,
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($userRepository->findOneBy(['email' => $user->getEmail()]) !== null) {
                $form->get('email')->addError(new FormError('Un compte existe déjà avec cet email.'));

                return $this->render('security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                (string) $form->get('plainPassword')->getData()
            );

            $user->setPassword($hashedPassword);
            $user->setActive(true);

            $now = new DateTime();
            $user->setCreatedAt($now);
            $user->setUpdatedAt($now);

            $entityManager->persist($user);
            try {
                $entityManager->flush();
            } catch (UniqueConstraintViolationException) {
                $form->get('email')->addError(new FormError('Un compte existe déjà avec cet email.'));

                return $this->render('security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            $email = $emailFactory->createWelcomeEmail($user);
            $mailer->send($email);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
