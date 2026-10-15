<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\Contact\ContactFormType;
use App\Service\Mail\EmailFactoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact', name: 'app_contact')]
final class ContactController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, EmailFactoryService $emailFactory): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactFormType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactMessage->setIp($request->getClientIp());
            $contactMessage->setcreatedAt(new \DateTime());
            $entityManager->persist($contactMessage);
            $entityManager->flush();
            $email = $emailFactory->createContactEmail($contactMessage);
            try {
                $mailer->send($email);
            } catch (\Exception $e) {
                return $this->redirectToRoute('app_contact_failed');
            }

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

    #[Route('/failed', name: '_failed', methods: ['GET'])]
    public function failed(): Response
    {
        return $this->render('contact/failed.html.twig');
    }
}
