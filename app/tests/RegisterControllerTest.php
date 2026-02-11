<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class RegisterControllerTest extends WebTestCase
{
    use MailerAssertionsTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();

        if (static::$kernel !== null) {
            $kernelContainer = static::$kernel->getContainer();
            $parameterBag = $kernelContainer->getParameterBag();

            if (!$parameterBag->has('appName')) {
                $parameters = $parameterBag->all();
                $parameters['appName'] = $_ENV['APP_NAME'] ?? 'Vite Gourmand';

                $newBag = new ParameterBag($parameters);
                $reflection = new \ReflectionProperty($kernelContainer, 'parameterBag');
                $reflection->setAccessible(true);
                $reflection->setValue($kernelContainer, $newBag);
            }
        }

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        foreach ($userRepository->findAll() as $user) {
            $entityManager->remove($user);
        }

        $entityManager->flush();
    }

    public function testRegisterSendsWelcomeEmail(): void
    {
        $crawler = $this->client->request('GET', '/register');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form.auth-form');

        $form = $crawler->filter('form.auth-form')->form([
            'registration_form[email]' => 'new.user@example.com',
            'registration_form[firstName]' => 'Alice',
            'registration_form[lastName]' => 'Durand',
            'registration_form[plainPassword]' => 'password123',
        ]);

        $this->client->submit($form);

        self::assertResponseRedirects('/login');
        self::assertQueuedEmailCount(1);

        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'From', 'no-reply@vite-gourmand.test');
        self::assertEmailAddressContains($email, 'To', 'new.user@example.com');
        self::assertEmailSubjectContains($email, 'Bienvenue');
        self::assertEmailHtmlBodyContains($email, 'Alice');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        self::assertNotNull($userRepository->findOneBy(['email' => 'new.user@example.com']));
    }
}
