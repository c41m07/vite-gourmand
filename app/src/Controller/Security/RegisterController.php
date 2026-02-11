<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager,
        MailerInterface             $mailer
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                (string)$form->get('plainPassword')->getData()
            );

            $user->setPassword($hashedPassword);
            $user->setActive(true);

            $now = new DateTime();
            $user->setCreatedAt($now);
            $user->setUpdatedAt($now);

            $entityManager->persist($user);
            $entityManager->flush();
            $email = new TemplatedEmail()
                ->from('no-reply@vite-gourmand.test')
                ->to($user->getEmail())
                ->subject('Bienvenue sur le site de ' . $this->getParameter('appName'))
                ->htmlTemplate('emails/welcome.html.twig')
                ->textTemplate('')
                ->context([
                    'firstName' => $user->getFirstName(),
                ]);
            $mailer->send($email);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}
