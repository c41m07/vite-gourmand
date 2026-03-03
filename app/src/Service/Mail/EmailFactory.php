<?php

namespace App\Service\Mail;

use App\Entity\ContactMessage;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


final readonly class EmailFactory
{
    public function __construct(
        #[Autowire('%no_reply_address%')]
        private string $noreplyEmail,
        #[Autowire('%owner_address%')]
        private string $ownerEmail,
        #[Autowire('%app_name%')]
        private string $appName,
    )
    {

    }


    public function createWelcomeEmail(User $user): TemplatedEmail
    {
        return new TemplatedEmail()
            ->from($this->noreplyEmail)
            ->to((string)$user->getEmail())
            ->subject('Bienvenue sur le site de ' . $this->appName)
            ->htmlTemplate('emails/welcome.html.twig')
            ->context([
                'firstName' => $user->getFirstName(),
            ]);

    }

    public function createContactEmail(ContactMessage $contactMessage): TemplatedEmail
    {
        return new TemplatedEmail()
            ->from($this->noreplyEmail)
            ->replyTo((string)$contactMessage->getEmail())
            ->to($this->ownerEmail)
            ->subject($contactMessage->getSubject())
            ->htmlTemplate('emails/contact.html.twig')
            ->context([
                'message' => (string)$contactMessage->getMessage(),
                'contactEmail' => (string)$contactMessage->getEmail(),
                'subject' => (string)$contactMessage->getSubject(),
                'firstName' => (string)$contactMessage->getFirstName(),
                'lastName' => (string)$contactMessage->getLastName(),
            ]);

    }
}
