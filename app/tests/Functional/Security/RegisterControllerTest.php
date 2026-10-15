<?php

namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Tests\Support\GeneratesTestPasswordsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterControllerTest extends WebTestCase
{
    use GeneratesTestPasswordsTrait;
    use MailerAssertionsTrait;

    private KernelBrowser $client;

    public function testRegisterSendsWelcomeEmail(): void
    {
        $plainPassword = self::generateTestPassword();

        $crawler = $this->client->request('GET', '/register');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form.auth-form');

        $form = $crawler->filter('form.auth-form')->form([
            'registration_form[email]' => 'new.user@example.com',
            'registration_form[firstName]' => 'Alice',
            'registration_form[lastName]' => 'Durand',
            'registration_form[plainPassword]' => $plainPassword,
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
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $userRepository = $entityManager->getRepository(User::class);

        self::assertNotNull($userRepository->findOneBy(['email' => 'new.user@example.com']));
    }

    public function testRegisterWithExistingEmailShowsError(): void
    {
        $existingUserPassword = self::generateTestPassword();
        $submittedPassword = self::generateTestPassword();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $existingUser = (new User())
            ->setEmail('existing.user@example.com')
            ->setFirstName('Existing')
            ->setLastName('User')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(true)
            ->setRoles(['ROLE_USER']);
        $existingUser->setPassword($passwordHasher->hashPassword($existingUser, $existingUserPassword));

        $entityManager->persist($existingUser);
        $entityManager->flush();

        $crawler = $this->client->request('GET', '/register');
        self::assertResponseIsSuccessful();

        $form = $crawler->filter('form.auth-form')->form([
            'registration_form[email]' => 'existing.user@example.com',
            'registration_form[firstName]' => 'Alice',
            'registration_form[lastName]' => 'Durand',
            'registration_form[plainPassword]' => $submittedPassword,
        ]);

        $this->client->submit($form);

        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'Un compte existe déjà avec cet email.',
            (string) $this->client->getResponse()->getContent()
        );

        $userRepository = $entityManager->getRepository(User::class);
        self::assertCount(1, $userRepository->findBy(['email' => 'existing.user@example.com']));
    }

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();

        if (null !== static::$kernel) {
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
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $userRepository = $entityManager->getRepository(User::class);

        $entityManager->getConnection()->executeStatement('DELETE FROM reset_password_request');

        foreach ($userRepository->findAll() as $user) {
            $entityManager->remove($user);
        }

        $entityManager->flush();
    }
}
