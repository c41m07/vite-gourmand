<?php

namespace App\Tests\Functional\User;

use App\Entity\User;
use App\Tests\Support\GeneratesTestPasswords;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileControllerTest extends WebTestCase
{
    use GeneratesTestPasswords;

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $this->entityManager->getConnection()->executeStatement('DELETE FROM reset_password_request');

        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();
    }

    public function testProfileAccessIsDeniedForAnonymous(): void
    {
        $this->client->request('GET', '/user/profile');

        self::assertResponseRedirects('/login');
    }

    public function testProfileAccessIsAllowedForRoleUser(): void
    {
        $userPassword = self::generateTestPassword();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $user = $this->createUser(
            'profile.user@example.com',
            'Profile',
            'User',
            $userPassword,
            $passwordHasher
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/user/profile?tab=profile');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Mon compte');
        self::assertSelectorTextContains('.account-section-title', 'Informations personnelles');
        self::assertStringContainsString('profile.user@example.com', (string) $this->client->getResponse()->getContent());
    }

    public function testEditProfileAccessIsDeniedForAnonymous(): void
    {
        $this->client->request('GET', '/user/profile/edit');

        self::assertResponseRedirects('/login');
    }

    public function testEditProfileAccessIsAllowedForRoleUser(): void
    {
        $userPassword = self::generateTestPassword();

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get('security.user_password_hasher');

        $user = $this->createUser(
            'profile.user@example.com',
            'Profile',
            'User',
            $userPassword,
            $passwordHasher
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/user/profile/edit');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Modifier mon profil');
        self::assertSelectorExists('form[name="profil_edit_form"]');
        self::assertSelectorExists('#profil_edit_form_currentPassword');
    }

    private function createUser(
        string $email,
        string $firstName,
        string $lastName,
        string $plainPassword,
        UserPasswordHasherInterface $passwordHasher
    ): User {
        $user = (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setActive(true)
            ->setRoles(['ROLE_USER']);

        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        return $user;
    }
}

