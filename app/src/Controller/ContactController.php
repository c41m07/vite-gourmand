<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact', name: 'app_contact')]
final class ContactController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactFormType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactMessage->setIp($request->getClientIp());
            $contactMessage->setcreatedAt(new DateTime());
            $entityManager->persist($contactMessage);
            $entityManager->flush();
            $email = new TemplatedEmail()
                ->from($contactMessage->getEmail())
                ->to($this->getParameter('owner_address'))
                ->subject($contactMessage->getSubject())
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'message' => $contactMessage->getMessage(),
                ]);
            $mailer->send($email);

            return $this->redirectToRoute('app_contact_success');
        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }

    #[Route('/success', name: '_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('contact/success.html.twig');
    }

}
