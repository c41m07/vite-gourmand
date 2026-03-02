<?php

namespace App\Tests;

use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    use MailerAssertionsTrait;

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM contact_message');
    }

    public function testContactSubmissionPersistsAndQueuesEmail(): void
    {
        $crawler = $this->client->request('GET', '/contact/');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form.contact-form__form');

        $form = $crawler->filter('form.contact-form__form')->form([
            'contact_form[firstName]' => 'Alice',
            'contact_form[lastName]' => 'Durand',
            'contact_form[email]' => 'alice@example.com',
            'contact_form[subject]' => 'Demande traiteur',
            'contact_form[message]' => 'Bonjour, je souhaite un devis pour 20 personnes.',
        ]);

        $this->client->submit($form);

        self::assertResponseRedirects('/contact/success');
        self::assertQueuedEmailCount(1);

        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'From', 'no-reply@vite-gourmand.test');
        self::assertEmailAddressContains($email, 'Reply-To', 'alice@example.com');
        self::assertEmailAddressContains($email, 'To', 'contact@vite-gourmand.test');
        self::assertEmailSubjectContains($email, 'Demande traiteur');
        self::assertEmailHtmlBodyContains($email, 'Alice Durand');
        self::assertEmailHtmlBodyContains($email, 'Bonjour, je souhaite un devis pour 20 personnes.');

        $contactMessage = $this->entityManager->getRepository(ContactMessage::class)->findOneBy([
            'email' => 'alice@example.com',
            'subject' => 'Demande traiteur',
        ]);

        self::assertNotNull($contactMessage);
        self::assertSame('Bonjour, je souhaite un devis pour 20 personnes.', $contactMessage->getMessage());
    }
}
